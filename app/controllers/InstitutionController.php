<?php
// ═══════════════════════════════════════════════
// CONTROLLER InstitutionController (anciennement "EtablissementController")
// Gère : tableau de bord établissement, profil, candidatures reçues
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Institution.php";
require_once __DIR__ . "/../models/User.php";

require_once __DIR__ . "/../models/Application.php";

class InstitutionController
{
    private PDO $pdo;
    private Institution $institutionModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo              = $pdo;
        $this->institutionModel = new Institution($pdo);
    }

    // ─────────────────────────────────────────────
    // Tableau de bord établissement
    // ─────────────────────────────────────────────

    public function home(): void
    {
        check_role('etablissement');

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('institution/home', ['etablissement' => $institution]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Profil établissement
    // ─────────────────────────────────────────────

    public function profile(): void
    {
        check_role('etablissement');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processProfileUpdate();
            return;
        }

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('institution/profile', ['etablissement' => $institution]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Candidatures reçues par l'établissement
    // ─────────────────────────────────────────────

    /**
     * Affiche les candidatures reçues par l'établissement, avec filtre par statut.
     * ?route=etablissement/candidatures&statut=en_attente
     */
    public function applications(): void
    {
        check_role('etablissement');

        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        // Lire le filtre de statut dans l'URL (optionnel)
        $status = filter_input(INPUT_GET, 'statut', FILTER_SANITIZE_SPECIAL_CHARS);
        $validStatuses = ['tous', 'en_attente', 'acceptee', 'refusee'];
        // Si le statut n'est pas valide, on affiche tous
        if (!in_array($status, $validStatuses, true)) {
            $status = 'tous';
        }

        $applicationModel = new Application($this->pdo);
        $applications     = $applicationModel->findByInstitution(
            $institution['id_etablissement'],
            $status === 'tous' ? null : $status
        );

        $this->render('layouts/header');
        $this->render('institution/applicants', [
            'etablissement' => $institution,
            'candidatures'  => $applications,
            'statut'        => $status,
        ]);
        $this->render('layouts/footer');
    }

    /**
     * Accepte ou refuse une candidature (POST uniquement).
     */
    public function processApplication(): void
    {
        check_role('etablissement');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=institution/applicants");
            exit();
        }

        $institution     = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);
        $applicationId   = (int) ($_POST['id_candidature'] ?? 0);
        $status          = trim($_POST['statut'] ?? '');
        $validStatuses   = ['acceptee', 'refusee'];

        // Validation stricte du statut (empêche l'injection d'une valeur arbitraire)
        if ($applicationId <= 0 || !in_array($status, $validStatuses, true)) {
            set_flash('error', 'Requête invalide');
            header("Location: index.php?route=institution/applicants");
            exit();
        }

        $applicationModel = new Application($this->pdo);
        // process() vérifie que la candidature appartient à cet établissement via la jointure SQL
        $success = $applicationModel->process(
            $applicationId,
            $institution['id_etablissement'],
            $status
        );

        if ($success) {
            $label = $status === 'acceptee' ? 'acceptée' : 'refusée';
            set_flash('success', "Candidature {$label} avec succès.");
        } else {
            set_flash('error', 'Impossible de traiter cette candidature (déjà traitée ou introuvable)');
        }

        header("Location: index.php?route=institution/applicants");
        exit();
    }

    /**
     * Logique de mise à jour du profil établissement.
     */
    private function processProfileUpdate(): void
    {
        $institution = $this->institutionModel->findByUserId($_SESSION['id_utilisateur']);

        if (!$institution) {
            set_flash('error', 'Profil introuvable.');
            header("Location: index.php?route=institution/home");
            exit();
        }

        $name    = trim(htmlspecialchars($_POST['nom']     ?? ''));
        $type    = trim($_POST['type']     ?? '');
        $website = trim($_POST['site_web'] ?? '');
        $city    = trim(htmlspecialchars($_POST['ville']   ?? ''));
        $address = trim(htmlspecialchars($_POST['adresse'] ?? ''));

        if (empty($name)) {
            set_flash('error', "Le nom de l'établissement est obligatoire.");
            header("Location: index.php?route=institution/profile");
            exit();
        }
        // Utilise Institution::validTypes() -- source unique de vérité
        if (!in_array($type, Institution::validTypes(), true)) {
            set_flash('error', "Type d'établissement invalide.");
            header("Location: index.php?route=institution/profile");
            exit();
        }
        // FILTER_VALIDATE_URL valide le format de l'URL (doit commencer par http/https)
        if (!empty($website) && !filter_var($website, FILTER_VALIDATE_URL)) {
            set_flash('error', "L'URL du site web est invalide");
            header("Location: index.php?route=institution/profile");
            exit();
        }

        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8) {
                set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                header("Location: index.php?route=institution/profile");
                exit();
            }
            if ($password !== $confirm) {
                set_flash('error', 'Les mots de passe ne correspondent pas.');
                header("Location: index.php?route=institution/profile");
                exit();
            }
        }

        try {
            // 1. Mettre à jour les infos générales de l'établissement
            $this->institutionModel->update(
                $institution['id_etablissement'],
                $name,
                $type,
                !empty($website) ? $website : null
            );

            // 2. Mettre à jour ou créer la localisation (upsert)
            $this->institutionModel->upsertLocation(
                $institution['id_etablissement'],
                !empty($city)    ? $city    : null,
                !empty($address) ? $address : null
            );

            // 3. Mettre à jour le mot de passe si renseigné
            if (!empty($password)) {
                $userModel = new User($this->pdo);
                $userModel->updatePassword(
                    $_SESSION['id_utilisateur'],
                    $password
                );
            }

            set_flash('success', 'Profil mis à jour avec succès.');
            header("Location: index.php?route=institution/profile");
            exit();
        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil établissement : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=institution/profile");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Rendu des vues
    // ─────────────────────────────────────────────

    /**
     * Inclut un fichier de vue en lui rendant disponibles les données fournies.
     * Les clés du tableau $data restent volontairement en français : ce sont
     * les noms de variables attendus par les vues (contrat d'interface inchangé).
     */
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}
