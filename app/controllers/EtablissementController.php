<?php
// ═══════════════════════════════════════════════
// CONTROLLER EtablissementController
// Gère : tableau de bord établissement, profil
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Etablissement.php";
require_once __DIR__ . "/../models/Utilisateur.php";

class EtablissementController
{
    private PDO $pdo;
    private Etablissement $etablissementModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo                = $pdo;
        $this->etablissementModel = new Etablissement($pdo);
    }

    // ─────────────────────────────────────────────
    // Tableau de bord établissement
    // ─────────────────────────────────────────────

    public function accueil(): void
    {
        check_role('etablissement');

        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('etablissement/accueil', ['etablissement' => $etablissement]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Profil établissement
    // ─────────────────────────────────────────────

    public function profil(): void
    {
        check_role('etablissement');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterProfil();
            return;
        }

        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('etablissement/profil', ['etablissement' => $etablissement]);
        $this->render('layouts/footer');
    }

    /**
     * Logique de mise à jour du profil établissement.
     */
    private function traiterProfil(): void
    {
        $etablissement = $this->etablissementModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        if (!$etablissement) {
            set_flash('error', 'Profil introuvable.');
            header("Location: index.php?route=etablissement/accueil");
            exit();
        }

        $nom      = trim(htmlspecialchars($_POST['nom']     ?? ''));
        $type     = trim($_POST['type']     ?? '');
        $siteWeb  = trim($_POST['site_web'] ?? '');
        $ville    = trim(htmlspecialchars($_POST['ville']   ?? ''));
        $adresse  = trim(htmlspecialchars($_POST['adresse'] ?? ''));

        if (empty($nom)) {
            set_flash('error', "Le nom de l'établissement est obligatoire.");
            header("Location: index.php?route=etablissement/profil");
            exit();
        }
        // Utilise Etablissement::typesValides() — source unique de vérité
        if (!in_array($type, Etablissement::typesValides(), true)) {
            set_flash('error', "Type d'établissement invalide.");
            header("Location: index.php?route=etablissement/profil");
            exit();
        }
        // FILTER_VALIDATE_URL valide le format de l'URL (doit commencer par http/https)
        if (!empty($siteWeb) && !filter_var($siteWeb, FILTER_VALIDATE_URL)) {
            set_flash('error', "L'URL du site web est invalide.");
            header("Location: index.php?route=etablissement/profil");
            exit();
        }

        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8) {
                set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                header("Location: index.php?route=etablissement/profil");
                exit();
            }
            if ($password !== $confirm) {
                set_flash('error', 'Les mots de passe ne correspondent pas.');
                header("Location: index.php?route=etablissement/profil");
                exit();
            }
        }

        try {
            // 1. Mettre à jour les infos générales de l'établissement
            $this->etablissementModel->mettreAJour(
                $etablissement['id_etablissement'],
                $nom,
                $type,
                !empty($siteWeb) ? $siteWeb : null
            );

            // 2. Mettre à jour ou créer la localisation (upsert)
            $this->etablissementModel->upsertLocalisation(
                $etablissement['id_etablissement'],
                !empty($ville)   ? $ville   : null,
                !empty($adresse) ? $adresse : null
            );

            // 3. Mettre à jour le mot de passe si renseigné
            if (!empty($password)) {
                $utilisateurModel = new Utilisateur($this->pdo);
                $utilisateurModel->mettreAJourMotDePasse(
                    $_SESSION['id_utilisateur'],
                    $password
                );
            }

            set_flash('success', 'Profil mis à jour avec succès.');
            header("Location: index.php?route=etablissement/profil");
            exit();

        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil établissement : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=etablissement/profil");
            exit();
        }
    }

    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}