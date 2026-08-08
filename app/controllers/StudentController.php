<?php
// ═══════════════════════════════════════════════
// CONTROLLER StudentController (anciennement "EtudiantController")
// Gère : tableau de bord étudiant, profil, catalogue, recommandations, candidatures
// ═══════════════════════════════════════════════

require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Degree.php";
require_once __DIR__ . "/../models/User.php";

require_once __DIR__ . "/../models/Application.php";
require_once __DIR__ . "/../models/Program.php";

class StudentController
{
    private PDO $pdo;
    private Student $studentModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo          = $pdo;
        $this->studentModel = new Student($pdo);
    }

    // ─────────────────────────────────────────────
    // Tableau de bord étudiant
    // ─────────────────────────────────────────────

    /**
     * Affiche le tableau de bord de l'étudiant connecté.
     */
    public function home(): void
    {
        // Sécurité : vérifier que l'utilisateur est connecté et a le rôle étudiant
        check_role('etudiant'); // Défini dans config/auth.php

        // Récupérer les données de l'étudiant via le Model
        $student = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        // Passer les données à la vue via la méthode render()
        $this->render('layouts/header');
        $this->render('student/home', ['etudiant' => $student]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Profil étudiant
    // ─────────────────────────────────────────────

    /**
     * GET  -> affiche le formulaire de profil pré-rempli
     * POST -> traite la mise à jour
     */
    public function profile(): void
    {
        check_role('etudiant');

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processProfileUpdate();
            return;
        }

        // Charger les données actuelles pour pré-remplir le formulaire
        $student = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        $this->render('layouts/header');
        $this->render('student/profile', ['etudiant' => $student]);
        $this->render('layouts/footer');
    }

    /**
     * Logique de mise à jour du profil étudiant (informations + bac + mot de passe).
     */
    private function processProfileUpdate(): void
    {
        $student = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        if (!$student) {
            set_flash('error', 'Profil introuvable');
            header("Location: index.php?route=student/home");
            exit();
        }

        $lastName  = trim(htmlspecialchars($_POST['nom']    ?? ''));
        $firstName = trim(htmlspecialchars($_POST['prenom'] ?? ''));
        $birthDate = trim($_POST['date_de_naissance'] ?? '');
        $series    = trim($_POST['serie_bac'] ?? '');
        $average   = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
        $examYear  = (int) ($_POST['annee_obtention'] ?? 0);

        $validSeries = ['A', 'C', 'D', 'L', 'OSE', 'S'];

        if (empty($lastName) || empty($firstName)) {
            set_flash('error', 'Nom et prénom sont obligatoires');
            header("Location: index.php?route=student/profile");
            exit();
        }
        if (!in_array($series, $validSeries, true)) {
            set_flash('error', 'Série de baccalauréat invalide.');
            header("Location: index.php?route=student/profile");
            exit();
        }

        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';

        if (!empty($password)) {
            if (strlen($password) < 8) {
                set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
                header("Location: index.php?route=student/profile");
                exit();
            }
            if ($password !== $confirm) {
                set_flash('error', 'Les mots de passe ne correspondent pas.');
                header("Location: index.php?route=student/profile");
                exit();
            }
        }

        try {
            // 1. Mettre à jour les informations personnelles
            $this->studentModel->update(
                $student['id_etudiant'],
                $lastName,
                $firstName,
                !empty($birthDate) ? $birthDate : null
            );

            // 2. Mettre à jour le diplôme (année d'obtention) et le bac (série, moyenne, mention)
            $degreeModel = new Degree($this->pdo);
            $degreeModel->updateYear($student['id_diplome'], $examYear);

            $honors = Student::computeHonors($average);
            $degreeModel->updateExam($student['id_bac'], $series, $average, $honors);

            // 3. Mettre à jour le mot de passe si renseigné
            if (!empty($password)) {
                $userModel = new User($this->pdo);
                $userModel->updatePassword($_SESSION['id_utilisateur'], $password);
            }

            set_flash('success', 'Profil mis à jour avec succès');
            header("Location: index.php?route=student/profile");
            exit();
        } catch (PDOException $e) {
            error_log("Erreur mise à jour profil étudiant : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer');
            header("Location: index.php?route=student/profile");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Catalogue des établissements
    // ─────────────────────────────────────────────

    /**
     * Affiche toutes les offres de filières disponibles.
     * Supporte la recherche textuelle via GET.
     */
    public function institutions(): void
    {
        check_role('etudiant');

        // Récupérer la recherche dans l'URL : ?route=etudiant/etablissements&q=info
        // filter_input sécurise la lecture du paramètre GET
        $search = trim(filter_input(INPUT_GET, 'q', FILTER_SANITIZE_SPECIAL_CHARS) ?? '');

        $programModel = new Program($this->pdo);
        $offers       = $programModel->findAllOffers(!empty($search) ? $search : null);

        $this->render('layouts/header');
        $this->render('student/institutions', [
            'offres'    => $offers,
            'recherche' => $search,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Recommandations personnalisées
    // ─────────────────────────────────────────────

    /**
     * GET  -> affiche les recommandations existantes (ou message si aucune)
     * POST -> (re)calcule les recommandations puis redirige en GET
     */
    public function recommendations(): void
    {
        check_role('etudiant');

        $programModel = new Program($this->pdo);
        $student      = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        // Mode POST : l'utilisateur a cliqué sur "Générer mes recommandations"
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Calcul et sauvegarde des recommandations en base
            $programModel->computeAndSaveRecommendations(
                $student['id_etudiant'],
                $student
            );
            set_flash('success', 'Recommandations générées avec succès.');
            // Redirection POST -> GET (pattern PRG) pour éviter le re-submit au refresh
            header("Location: index.php?route=student/recommendations");
            exit();
        }

        // Mode GET : récupérer les recommandations sauvegardées
        $recommendations = $programModel->findRecommendationsByStudent($student['id_etudiant']);

        $this->render('layouts/header');
        $this->render('student/recommendations', [
            'etudiant'        => $student,
            'recommandations' => $recommendations,
        ]);
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Candidatures de l'étudiant
    // ─────────────────────────────────────────────

    /**
     * Affiche la liste des candidatures de l'étudiant connecté.
     */
    public function applications(): void
    {
        check_role('etudiant');
        $student         = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);
        $applicationModel = new Application($this->pdo);
        $applications     = $applicationModel->findByStudent($student['id_etudiant']);

        $this->render('layouts/header');
        $this->render('student/applications', ['candidatures' => $applications]);
        $this->render('layouts/footer');
    }

    /**
     * Traite la soumission d'une candidature (POST uniquement).
     * Redirige vers la liste des candidatures après soumission.
     */
    public function submitApplication(): void
    {
        check_role('etudiant');

        // Sécurité : cette route n'accepte que les requêtes POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=student/institutions");
            exit();
        }

        $offerId = (int) ($_POST['id_offre_filiere'] ?? 0);
        $student = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        if ($offerId <= 0 || !$student) {
            set_flash('error', 'Requête invalide.');
            header("Location: index.php?route=student/institutions");
            exit();
        }

        $applicationModel = new Application($this->pdo);

        // Vérifier si l'étudiant a déjà postulé à cette offre
        if ($applicationModel->alreadyExists($student['id_etudiant'], $offerId)) {
            set_flash('error', 'Vous avez déjà postulé à cette formation');
            header("Location: index.php?route=student/applications");
            exit();
        }

        try {
            $applicationModel->submit($student['id_etudiant'], $offerId, null);
            set_flash('success', 'Votre candidature a été envoyée avec succès !');
        } catch (PDOException $e) {
            error_log("Erreur soumission candidature : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
        }

        header("Location: index.php?route=student/applications");
        exit();
    }

    /**
     * Annule une candidature 'en_attente' (POST uniquement).
     */
    public function cancelApplication(): void
    {
        check_role('etudiant');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header("Location: index.php?route=student/applications");
            exit();
        }

        $applicationId = (int) ($_POST['id_candidature'] ?? 0);
        $student       = $this->studentModel->findByUserId($_SESSION['id_utilisateur']);

        if ($applicationId <= 0 || !$student) {
            set_flash('error', 'Requête invalide');
            header("Location: index.php?route=student/applications");
            exit();
        }

        $applicationModel = new Application($this->pdo);
        // cancel() retourne true si la mise à jour a eu lieu
        $success = $applicationModel->cancel($applicationId, $student['id_etudiant']);

        if ($success) {
            set_flash('success', 'Candidature annulée avec succès');
        } else {
            set_flash('error', 'Impossible d\'annuler cette candidature (déjà traitée ou introuvable)');
        }

        header("Location: index.php?route=student/applications");
        exit();
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
