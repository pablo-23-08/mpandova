<?php
// ═══════════════════════════════════════════════
// CONTROLLER EtudiantController
// Gère : tableau de bord étudiant, profil
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Etudiant.php";
require_once __DIR__ . "/../models/Diplome.php";
require_once __DIR__ . "/../models/Utilisateur.php";

class EtudiantController
{
    private PDO $pdo;
    private Etudiant $etudiantModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo           = $pdo;
        $this->etudiantModel = new Etudiant($pdo);
    }

    // ─────────────────────────────────────────────
    // Tableau de bord étudiant
    // ─────────────────────────────────────────────

    /**
     * Affiche le tableau de bord de l'étudiant connecté.
     */
    public function accueil(): void
    {
        // Sécurité : vérifier que l'utilisateur est connecté et a le rôle étudiant
        check_role('etudiant'); // Défini dans config/auth.php

        // Récupérer les données de l'étudiant via le Model
        $etudiant = $this->etudiantModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        // Passer les données à la vue via la méthode render()
        $this->render('layouts/header');
        $this->render('etudiant/accueil', ['etudiant' => $etudiant]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Profil étudiant
    // ─────────────────────────────────────────────

    /**
     * GET  → affiche le formulaire de profil pré-rempli
     * POST → traite la mise à jour
     */
    public function profil(): void
    {
        check_role('etudiant');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterProfil();
            return;
        }

        // Charger les données actuelles pour pré-remplir le formulaire
        $etudiant = $this->etudiantModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('etudiant/profil', ['etudiant' => $etudiant]);
        $this->render('layouts/footer');
    }

    /**
     * Logique de mise à jour du profil étudiant.
     */
    private function traiterProfil(): void
    {
        // Récupérer l'étudiant pour avoir son id_etudiant
        $etudiant = $this->etudiantModel->findByIdUtilisateur($_SESSION['id_utilisateur']);

        if (!$etudiant) {
            set_flash('error', 'Profil introuvable.');
            header("Location: index.php?route=etudiant/accueil");
            exit();
        }

        // Récupérer et sécuriser les données du formulaire
        $nom    = trim(htmlspecialchars($_POST['nom']    ?? ''));
        $prenom = trim(htmlspecialchars($_POST['prenom'] ?? ''));
        $ddn    = $_POST['date_de_naissance'] ?? '';
        $serie  = trim($_POST['serie_bac']    ?? '');
        $annee  = (int) ($_POST['annee_bac']   ?? 0);

        // Conversion virgule → point pour la notation décimale française
        // "14,5" (saisie française) → "14.5" (format PHP/MySQL)
        $moyenne = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);

        $seriesValides = ['A', 'C', 'D', 'L', 'OSE', 'S'];

        // Validations
        if (empty($nom) || empty($prenom)) {
            set_flash('error', 'Nom et prénom sont obligatoires.');
            header("Location: index.php?route=etudiant/profil");
            exit();
        }
        if (!empty($serie) && !in_array($serie, $seriesValides, true)) {
            set_flash('error', 'Série de baccalauréat invalide.');
            header("Location: index.php?route=etudiant/profil");
            exit();
        }
        // DateTime::createFromFormat valide strictement le format de date
        if (!empty($ddn) && !DateTime::createFromFormat('Y-m-d', $ddn)) {
            set_flash('error', 'Date de naissance invalide.');
            header("Location: index.php?route=etudiant/profil");
            exit();
        }

        // Validation du mot de passe (optionnel : vide = pas de changement)
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8) {
                set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                header("Location: index.php?route=etudiant/profil");
                exit();
            }
            if ($password !== $confirm) {
                set_flash('error', 'Les mots de passe ne correspondent pas.');
                header("Location: index.php?route=etudiant/profil");
                exit();
            }
        }

        try {
            // 1. Mettre à jour les infos personnelles (table etudiant)
            $this->etudiantModel->mettreAJour(
                $etudiant['id_etudiant'],
                $nom,
                $prenom,
                !empty($ddn) ? $ddn : null
            );

            // 2. Mettre à jour le bac si la série et l'année sont renseignées
            if (!empty($serie) && $annee > 0) {
                $diplomeModel = new Diplome($this->pdo);
                // Calculer la mention à partir de la moyenne (méthode statique)
                $mention = Etudiant::calculerMention($moyenne);

                $diplomeModel->mettreAJourAnnee($etudiant['id_diplome'], $annee);
                $diplomeModel->mettreAJourBac($etudiant['id_bac'], $serie, $moyenne, $mention);
            }

            // 3. Mettre à jour le mot de passe si renseigné
            if (!empty($password)) {
                $utilisateurModel = new Utilisateur($this->pdo);
                $utilisateurModel->mettreAJourMotDePasse(
                    $_SESSION['id_utilisateur'],
                    $password
                );
            }

            set_flash('success', 'Profil mis à jour avec succès.');
            header("Location: index.php?route=etudiant/profil");
            exit();

        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil étudiant : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=etudiant/profil");
            exit();
        }
    }

    // Méthode utilitaire : rendu d'une vue (identique à AuthController)
    protected function render(string $view, array $data = []): void
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . "/../views/{$view}.php";
    }
}