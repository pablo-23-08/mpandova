<?php
// ═══════════════════════════════════════════════
// CONTROLLER AdminController
// Gère : tableau de bord admin, utilisateurs, filières (catalogue),
//        établissements (lecture), candidatures (lecture)
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
    // Tableau de bord
    // ─────────────────────────────────────────────

    /**
     * Page d'accueil de l'espace admin.
     * Affiche les statistiques globales et les raccourcis vers chaque section.
     */
    public function home(): void
    {
        // check_role('admin') appelle check_auth() puis vérifie $_SESSION['role'] === 'admin'
        // → redirige vers index.php si non connecté ou mauvais rôle
        check_role('admin');

        $stats = $this->adminModel->getStats();

        $this->render('layouts/header');
        $this->render('admin/home', ['stats' => $stats]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Gestion des utilisateurs
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste de tous les utilisateurs (hors admin).
     */
    public function users(): void
    {
        check_role('admin');

        $users = $this->adminModel->findAllUsers();

        $this->render('layouts/header');
        $this->render('admin/users', ['utilisateurs' => $users]);
        $this->render('layouts/footer');
    }

    /**
     * Supprime un compte utilisateur (étudiant ou établissement).
     * La suppression en cascade (définie en BDD) retire toutes ses données liées.
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

        // Vérifier que l'utilisateur existe ET n'est pas un admin
        // (double protection : la BDD a aussi une garde AND role != 'admin')
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
    // Gestion du catalogue de filières
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste des filières + formulaire d'ajout/modification.
     * Si ?edit=N est dans l'URL, le formulaire se pré-remplit avec la filière N.
     */
    public function programs(): void
    {
        check_role('admin');

        // Lire le paramètre ?edit= pour passer en mode "modification"
        $editId  = (int) ($_GET['edit'] ?? 0);
        // findProgramById retourne false si l'ID n'existe pas → formulaire d'ajout
        $program = $editId > 0 ? $this->adminModel->findProgramById($editId) : null;
        $programs = $this->adminModel->findAllPrograms();

        $this->render('layouts/header');
        $this->render('admin/programs', [
            'filieres'    => $programs,
            'modeEdition' => $program ?: null,  // null = mode ajout
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
            set_flash('success', 'Filière « ' . $nom . ' » ajoutée au catalogue.');
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
     * Route : index.php?route=admin/program/delete&id=3
     * La CASCADE BDD supprime aussi les offres et candidatures liées.
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
    // Vue globale des établissements
    // ─────────────────────────────────────────────

    /**
     * Affiche tous les établissements inscrits sur la plateforme.
     * Lecture seule : chaque établissement gère son propre profil.
     */
    public function institutions(): void
    {
        check_role('admin');

        $institutions = $this->adminModel->findAllInstitutions();

        $this->render('layouts/header');
        $this->render('admin/institutions', ['etablissements' => $institutions]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Vue globale des candidatures
    // ─────────────────────────────────────────────

    /**
     * Affiche toutes les candidatures de la plateforme avec filtre par statut.
     * Lecture seule : l'admin observe, les établissements traitent.
     */
    public function applications(): void
    {
        check_role('admin');

        $status = filter_input(INPUT_GET, 'statut', FILTER_SANITIZE_SPECIAL_CHARS);
        $validStatuses = ['tous', 'en_attente', 'acceptee', 'refusee', 'annulee'];

        if (!in_array($status, $validStatuses, true)) {
            $status = 'tous';
        }

        $applications = $this->adminModel->findAllApplications(
            $status === 'tous' ? null : $status
        );

        $this->render('layouts/header');
        $this->render('admin/applications', [
            'candidatures' => $applications,
            'statut'       => $status,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Rendu des vues
    // ─────────────────────────────────────────────

    /**
     * Même logique que dans les autres Controllers :
     * extract() rend les clés de $data disponibles comme variables PHP dans la vue.
     * EXTR_SKIP : ne remplace pas les variables déjà définies (sécurité).
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}