<?php
// ═══════════════════════════════════════════════
// CONTROLLER AdminController
// Gère : tableau de bord admin, utilisateurs, établissements,
//        formations (offres), filières (catalogue), candidatures
// Chaque méthode publique correspond à une route dans routes.php.
// ═══════════════════════════════════════════════

require_once __DIR__ . '/../models/Admin.php';
require_once __DIR__ . '/../models/User.php';

class AdminController
{
    private PDO   $pdo;
    private Admin $adminModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo        = $pdo;
        $this->adminModel = new Admin($pdo);
    }

    // ─────────────────────────────────────────────
    // 1. Tableau de bord
    // ─────────────────────────────────────────────

    public function home(): void
    {
        check_role('admin');

        $stats               = $this->adminModel->getStats();
        $recentApplications  = $this->adminModel->getRecentApplications(5);
        $recentInstitutions  = $this->adminModel->getRecentInstitutions(5);

        $this->render('layouts/header');
        $this->render('admin/home', [
            'stats'              => $stats,
            'recentApplications' => $recentApplications,
            'recentInstitutions' => $recentInstitutions,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // 2. Gestion des utilisateurs
    // ─────────────────────────────────────────────

    /**
     * Liste de tous les utilisateurs avec recherche et filtre de rôle.
     */
    public function users(): void
    {
        check_role('admin');

        $role   = filter_input(INPUT_GET, 'role',   FILTER_SANITIZE_SPECIAL_CHARS) ?: 'tous';
        $search = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $validRoles = ['tous', 'etudiant', 'etablissement', 'admin'];
        if (!in_array($role, $validRoles, true)) {
            $role = 'tous';
        }

        $users = $this->adminModel->findAllUsers(
            $role !== 'tous' ? $role : null,
            !empty($search)  ? $search : null
        );

        $this->render('layouts/header');
        $this->render('admin/users', [
            'utilisateurs' => $users,
            'roleFiltre'   => $role,
            'search'       => $search,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Affiche les détails d'un utilisateur.
     * Route : index.php?route=admin/user/view&id=5
     */
    public function viewUser(): void
    {
        check_role('admin');

        $userId = (int) ($_GET['id'] ?? 0);

        if ($userId <= 0) {
            set_flash('error', 'Identifiant invalide.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $user = $this->adminModel->findUserById($userId);

        if (!$user) {
            set_flash('error', 'Utilisateur introuvable.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/user_view', ['utilisateur' => $user]);
        $this->render('layouts/footer');
    }

    /**
     * Formulaire d'ajout d'un utilisateur (GET) + traitement (POST).
     * Route : index.php?route=admin/user/add
     */
    public function addUser(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processAddUser();
            return;
        }

        $this->render('layouts/header');
        $this->render('admin/user_form', ['modeEdition' => null]);
        $this->render('layouts/footer');
    }

    /**
     * Formulaire de modification d'un utilisateur (GET) + traitement (POST).
     * Route : index.php?route=admin/user/edit&id=5
     */
    public function editUser(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditUser();
            return;
        }

        $userId = (int) ($_GET['id'] ?? 0);

        if ($userId <= 0) {
            set_flash('error', 'Identifiant invalide.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $user = $this->adminModel->findUserById($userId);

        if (!$user) {
            set_flash('error', 'Utilisateur introuvable.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/user_form', ['modeEdition' => $user]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement de l'ajout d'un utilisateur.
     */
    private function processAddUser(): void
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $role     = trim($_POST['role']        ?? '');

        $validRoles = ['etudiant', 'etablissement', 'admin'];

        if (!$email) {
            set_flash('error', 'Adresse email invalide.');
            header("Location: index.php?route=admin/user/add");
            exit();
        }

        if (!in_array($role, $validRoles, true)) {
            set_flash('error', 'Rôle invalide.');
            header("Location: index.php?route=admin/user/add");
            exit();
        }

        if (strlen($password) < 8) {
            set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            header("Location: index.php?route=admin/user/add");
            exit();
        }

        if ($password !== $confirm) {
            set_flash('error', 'Les mots de passe ne correspondent pas.');
            header("Location: index.php?route=admin/user/add");
            exit();
        }

        if ($this->adminModel->emailExists($email)) {
            set_flash('error', 'Cet email est déjà utilisé.');
            header("Location: index.php?route=admin/user/add");
            exit();
        }

        try {
            $this->adminModel->createUser($email, $password, $role);
            set_flash('success', "Utilisateur « {$email} » créé avec succès.");
        } catch (PDOException $e) {
            error_log("Erreur création utilisateur (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/users");
        exit();
    }

    /**
     * Traitement de la modification d'un utilisateur.
     */
    private function processEditUser(): void
    {
        $userId   = (int) ($_POST['id_utilisateur'] ?? 0);
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $role     = trim($_POST['role']     ?? '');
        $password = $_POST['password']      ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        $validRoles = ['etudiant', 'etablissement', 'admin'];

        if ($userId <= 0 || !$email || !in_array($role, $validRoles, true)) {
            set_flash('error', 'Données invalides.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $user = $this->adminModel->findUserById($userId);

        if (!$user) {
            set_flash('error', 'Utilisateur introuvable.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        // Vérifier si le nouvel email est disponible (hors l'utilisateur actuel)
        if ($this->adminModel->emailExists($email, $userId)) {
            set_flash('error', 'Cet email est déjà utilisé par un autre compte.');
            header("Location: index.php?route=admin/user/edit&id={$userId}");
            exit();
        }

        try {
            $this->adminModel->updateUserEmail($userId, $email);
            $this->adminModel->updateUserRole($userId, $role);

            if (!empty($password)) {
                if (strlen($password) < 8) {
                    set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                    header("Location: index.php?route=admin/user/edit&id={$userId}");
                    exit();
                }
                if ($password !== $confirm) {
                    set_flash('error', 'Les mots de passe ne correspondent pas.');
                    header("Location: index.php?route=admin/user/edit&id={$userId}");
                    exit();
                }
                $this->adminModel->resetUserPassword($userId, $password);
            }

            set_flash('success', 'Utilisateur mis à jour avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur modification utilisateur (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/users");
        exit();
    }

    /**
     * Supprime un compte utilisateur.
     * Route : index.php?route=admin/user/delete&id=5
     */
    public function deleteUser(): void
    {
        check_role('admin');

        $userId = (int) ($_GET['id'] ?? 0);

        if ($userId <= 0) {
            set_flash('error', 'Identifiant invalide.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        $user = $this->adminModel->findUserById($userId);

        if (!$user || $user['role'] === 'admin') {
            set_flash('error', 'Utilisateur introuvable ou suppression non autorisée.');
            header("Location: index.php?route=admin/users");
            exit();
        }

        try {
            $this->adminModel->deleteUser($userId);
            set_flash('success', 'Compte supprimé avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur suppression utilisateur (admin) : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer ce compte. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/users");
        exit();
    }

    // ─────────────────────────────────────────────
    // 3. Gestion des établissements
    // ─────────────────────────────────────────────

    /**
     * Liste tous les établissements.
     */
    public function institutions(): void
    {
        check_role('admin');

        $search       = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $institutions = $this->adminModel->findAllInstitutions(!empty($search) ? $search : null);

        $this->render('layouts/header');
        $this->render('admin/institutions', [
            'etablissements' => $institutions,
            'search'         => $search,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Affiche le détail d'un établissement.
     * Route : index.php?route=admin/institution/view&id=3
     */
    public function viewInstitution(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        $institution  = $this->adminModel->findInstitutionById($id);
        $programs     = $this->adminModel->findInstitutionPrograms($id);
        $applications = $this->adminModel->findInstitutionApplications($id, 10);

        if (!$institution) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/institution_view', [
            'etablissement' => $institution,
            'programmes'    => $programs,
            'candidatures'  => $applications,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Formulaire de modification d'un établissement (GET) + traitement (POST).
     * Route : index.php?route=admin/institution/edit&id=3
     */
    public function editInstitution(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditInstitution();
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        $institution = $this->adminModel->findInstitutionById($id);

        if (!$institution) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/institution_form', ['etablissement' => $institution]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement de la modification d'un établissement.
     */
    private function processEditInstitution(): void
    {
        $id          = (int) ($_POST['id_etablissement'] ?? 0);
        $nom         = trim(htmlspecialchars($_POST['nom']         ?? ''));
        $type        = trim($_POST['type']        ?? '');
        $siteWeb     = trim($_POST['site_web']    ?? '');
        $description = trim($_POST['description'] ?? '');
        $ville       = trim(htmlspecialchars($_POST['ville']   ?? ''));
        $adresse     = trim(htmlspecialchars($_POST['adresse'] ?? ''));
        $region      = trim(htmlspecialchars($_POST['region']  ?? ''));

        $validTypes = ['universite_publique', 'universite_privee', 'grande_ecole', 'institut', 'autre'];

        if ($id <= 0 || empty($nom) || !in_array($type, $validTypes, true)) {
            set_flash('error', 'Données invalides.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        if (!empty($siteWeb) && !filter_var($siteWeb, FILTER_VALIDATE_URL)) {
            set_flash('error', "L'URL du site web est invalide.");
            header("Location: index.php?route=admin/institution/edit&id={$id}");
            exit();
        }

        try {
            $this->adminModel->updateInstitution(
                $id,
                $nom,
                $type,
                !empty($siteWeb)     ? $siteWeb     : null,
                !empty($description) ? $description : null
            );

            $this->adminModel->upsertInstitutionLocation(
                $id,
                !empty($ville)   ? $ville   : null,
                !empty($adresse) ? $adresse : null,
                !empty($region)  ? $region  : null
            );

            set_flash('success', "Établissement « {$nom} » mis à jour avec succès.");
        } catch (PDOException $e) {
            error_log("Erreur modification établissement (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/institution/view&id={$id}");
        exit();
    }

    /**
     * Supprime un établissement.
     * Route : index.php?route=admin/institution/delete&id=3
     */
    public function deleteInstitution(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        $institution = $this->adminModel->findInstitutionById($id);

        if (!$institution) {
            set_flash('error', 'Établissement introuvable.');
            header("Location: index.php?route=admin/institutions");
            exit();
        }

        try {
            $this->adminModel->deleteInstitution($id);
            set_flash('success', "Établissement supprimé avec succès.");
        } catch (PDOException $e) {
            error_log("Erreur suppression établissement (admin) : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer cet établissement.');
        }

        header("Location: index.php?route=admin/institutions");
        exit();
    }

    // ─────────────────────────────────────────────
    // 4. Gestion des formations (offres de filières)
    // ─────────────────────────────────────────────

    /**
     * Liste toutes les formations avec filtres.
     * Route : index.php?route=admin/formations
     */
    public function formations(): void
    {
        check_role('admin');

        $search        = trim(filter_input(INPUT_GET, 'q',              FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $institutionId = (int) ($_GET['etablissement'] ?? 0);
        $level         = trim(filter_input(INPUT_GET, 'niveau',         FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $offers       = $this->adminModel->findAllOffers(
            !empty($search)        ? $search        : null,
            $institutionId > 0     ? $institutionId : null,
            !empty($level)         ? $level         : null
        );
        $institutions = $this->adminModel->getInstitutionsForSelect();

        $this->render('layouts/header');
        $this->render('admin/formations', [
            'offres'           => $offers,
            'institutions'     => $institutions,
            'search'           => $search,
            'etablissementFiltre' => $institutionId,
            'niveauFiltre'     => $level,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Affiche le détail d'une formation.
     * Route : index.php?route=admin/formation/view&id=2
     */
    public function viewFormation(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        $offer   = $this->adminModel->findOfferById($id);

        if (!$offer) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        $debouches = $this->adminModel->findProgramOutlets($offer['id_filiere']);

        $this->render('layouts/header');
        $this->render('admin/formation_view', [
            'offre'     => $offer,
            'debouches' => $debouches,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Formulaire de modification d'une formation (GET) + traitement (POST).
     * Route : index.php?route=admin/formation/edit&id=2
     */
    public function editFormation(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processEditFormation();
            return;
        }

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        $offer = $this->adminModel->findOfferById($id);

        if (!$offer) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/formation_form', ['offre' => $offer]);
        $this->render('layouts/footer');
    }

    /**
     * Traitement de la modification d'une formation.
     */
    private function processEditFormation(): void
    {
        $id             = (int) ($_POST['id_offre_filiere'] ?? 0);
        $frais          = (float) ($_POST['frais_scolarite']  ?? 0);
        $places         = (int) ($_POST['place_disponible']   ?? 0);
        $duree          = trim(htmlspecialchars($_POST['duree_formation'] ?? ''));

        if ($id <= 0) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        try {
            $this->adminModel->updateOffer($id, $frais, $places, $duree);
            set_flash('success', 'Formation mise à jour avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur modification formation (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/formation/view&id={$id}");
        exit();
    }

    /**
     * Supprime une formation (offre de filière).
     * Route : index.php?route=admin/formation/delete&id=2
     */
    public function deleteFormation(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Formation introuvable.');
            header("Location: index.php?route=admin/formations");
            exit();
        }

        try {
            $this->adminModel->deleteOffer($id);
            set_flash('success', 'Formation supprimée avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur suppression formation (admin) : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer cette formation.');
        }

        header("Location: index.php?route=admin/formations");
        exit();
    }

    // ─────────────────────────────────────────────
    // Gestion du catalogue de filières (référentiel)
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste des filières + formulaire d'ajout/modification.
     */
    public function programs(): void
    {
        check_role('admin');

        $search  = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $editId  = (int) ($_GET['edit'] ?? 0);
        $program = $editId > 0 ? $this->adminModel->findProgramById($editId) : null;
        $programs = $this->adminModel->findAllPrograms(!empty($search) ? $search : null);

        $this->render('layouts/header');
        $this->render('admin/programs', [
            'filieres'    => $programs,
            'modeEdition' => $program ?: null,
            'search'      => $search,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Traite le formulaire d'ajout d'une filière (POST uniquement).
     */
    public function createProgram(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=admin/programs");
            exit();
        }

        $nom         = trim(htmlspecialchars($_POST['nom'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        if (empty($nom)) {
            set_flash('error', 'Le nom de la filière est obligatoire.');
            header("Location: index.php?route=admin/programs");
            exit();
        }

        try {
            $this->adminModel->createProgram(
                $nom,
                !empty($description) ? $description : null
            );
            set_flash('success', "Filière « {$nom} » ajoutée au catalogue.");
        } catch (PDOException $e) {
            error_log("Erreur création filière (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/programs");
        exit();
    }

    /**
     * Traite le formulaire de modification d'une filière (POST uniquement).
     */
    public function editProgram(): void
    {
        check_role('admin');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=admin/programs");
            exit();
        }

        $id          = (int) ($_POST['id_filiere'] ?? 0);
        $nom         = trim(htmlspecialchars($_POST['nom'] ?? ''));
        $description = trim($_POST['description'] ?? '');

        if ($id <= 0 || empty($nom)) {
            set_flash('error', 'Données invalides.');
            header("Location: index.php?route=admin/programs");
            exit();
        }

        try {
            $this->adminModel->updateProgram(
                $id,
                $nom,
                !empty($description) ? $description : null
            );
            set_flash('success', 'Filière mise à jour avec succès.');
        } catch (PDOException $e) {
            error_log("Erreur modification filière (admin) : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=admin/programs");
        exit();
    }

    /**
     * Supprime une filière du catalogue.
     */
    public function deleteProgram(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Filière introuvable.');
            header("Location: index.php?route=admin/programs");
            exit();
        }

        try {
            $this->adminModel->deleteProgram($id);
            set_flash('success', 'Filière supprimée du catalogue.');
        } catch (PDOException $e) {
            error_log("Erreur suppression filière (admin) : " . $e->getMessage());
            set_flash('error', 'Impossible de supprimer cette filière.');
        }

        header("Location: index.php?route=admin/programs");
        exit();
    }

    // ─────────────────────────────────────────────
    // 5. Vue globale des candidatures
    // ─────────────────────────────────────────────

    /**
     * Affiche toutes les candidatures avec filtres.
     */
    public function applications(): void
    {
        check_role('admin');

        $status        = filter_input(INPUT_GET, 'statut',       FILTER_SANITIZE_SPECIAL_CHARS) ?? 'tous';
        $institutionId = (int) ($_GET['etablissement'] ?? 0);
        $offerId       = (int) ($_GET['offre']         ?? 0);
        $search        = trim(filter_input(INPUT_GET, 'q',        FILTER_SANITIZE_SPECIAL_CHARS) ?? '');
        $dateFrom      = filter_input(INPUT_GET, 'date_debut',    FILTER_SANITIZE_SPECIAL_CHARS) ?? '';
        $dateTo        = filter_input(INPUT_GET, 'date_fin',      FILTER_SANITIZE_SPECIAL_CHARS) ?? '';

        $validStatuses = ['tous', 'en_attente', 'acceptee', 'refusee', 'annulee'];
        if (!in_array($status, $validStatuses, true)) {
            $status = 'tous';
        }

        $applications = $this->adminModel->findAllApplications(
            $status !== 'tous'  ? $status        : null,
            $institutionId > 0  ? $institutionId : null,
            $offerId > 0        ? $offerId       : null,
            !empty($search)     ? $search        : null,
            !empty($dateFrom)   ? $dateFrom      : null,
            !empty($dateTo)     ? $dateTo        : null
        );

        $appStats     = $this->adminModel->getApplicationStats();
        $institutions = $this->adminModel->getInstitutionsForSelect();

        $this->render('layouts/header');
        $this->render('admin/applications', [
            'candidatures'        => $applications,
            'statut'              => $status,
            'appStats'            => $appStats,
            'institutions'        => $institutions,
            'etablissementFiltre' => $institutionId,
            'offreFiltre'         => $offerId,
            'search'              => $search,
            'dateFrom'            => $dateFrom,
            'dateTo'              => $dateTo,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Affiche le détail d'une candidature.
     * Route : index.php?route=admin/application/view&id=12
     */
    public function viewApplication(): void
    {
        check_role('admin');

        $id = (int) ($_GET['id'] ?? 0);

        if ($id <= 0) {
            set_flash('error', 'Candidature introuvable.');
            header("Location: index.php?route=admin/applications");
            exit();
        }

        $application = $this->adminModel->findApplicationById($id);

        if (!$application) {
            set_flash('error', 'Candidature introuvable.');
            header("Location: index.php?route=admin/applications");
            exit();
        }

        $this->render('layouts/header');
        $this->render('admin/application_view', ['candidature' => $application]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Rendu des vues
    // ─────────────────────────────────────────────

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}
