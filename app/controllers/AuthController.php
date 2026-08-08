<?php
// ═══════════════════════════════════════════════
// CONTROLLER AuthController
// Gère : page d'accueil, connexion, déconnexion, inscription
// ═══════════════════════════════════════════════

// Charger les Models dont ce Controller a besoin (noms de classes/fichiers traduits en anglais)
require_once __DIR__ . "/../models/User.php";
require_once __DIR__ . "/../models/Student.php";
require_once __DIR__ . "/../models/Degree.php";
require_once __DIR__ . "/../models/Institution.php";

class AuthController
{
    private PDO $pdo;        // Connexion à la base de données
    private User $userModel; // Instance du Model User

    // Constructeur : injecte la connexion PDO et instancie les Models nécessaires
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->userModel = new User($pdo);
    }

    // ─────────────────────────────────────────────
    // Page d'accueil publique
    // ─────────────────────────────────────────────

    /**
     * Affiche la page d'accueil.
     * Si l'utilisateur est déjà connecté, le redirige vers son espace.
     */
    public function home(): void
    {
        redirect_if_logged(); // Défini dans config/auth.php
        $this->render('layouts/header');  // Inclure l'en-tête
        $this->render('home');            // Contenu de la page d'accueil
        $this->render('layouts/footer'); // Inclure le pied de page
    }

    // ─────────────────────────────────────────────
    // Connexion
    // ─────────────────────────────────────────────

    /**
     * GET  -> affiche le formulaire de connexion
     * POST -> traite le formulaire et connecte l'utilisateur
     */
    public function login(): void
    {
        redirect_if_logged();

        // Si la requête est un envoi de formulaire (POST) -> traiter les données
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processLogin();
            return;
        }

        // Sinon (GET) -> afficher le formulaire
        $this->render('layouts/header');
        $this->render('auth/login');
        $this->render('layouts/footer');
    }

    /**
     * Logique de traitement du formulaire de connexion.
     * Méthode privée : appelée uniquement par login() en cas de POST.
     */
    private function processLogin(): void
    {
        // filter_input sécurise la récupération : valide le format email
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validation des champs
        if (!$email || empty($password)) {
            set_flash('error', 'Veuillez remplir tous les champs correctement');
            header("Location: index.php?route=auth/login");
            exit();
        }

        // Chercher l'utilisateur par email (Model User)
        $user = $this->userModel->findByEmail($email);

        // Vérifier le mot de passe : password_verify compare le clair avec le hash stocké
        if (!$user || !password_verify($password, $user['mot_de_passe_hash'])) {
            set_flash('error', 'Email ou mot de passe incorrect.');
            header("Location: index.php?route=auth/login");
            exit();
        }

        // Succès -> créer la session PHP
        session_regenerate_id(true); // Prévention de la fixation de session
        $_SESSION['id_utilisateur'] = $user['id_utilisateur'];
        $_SESSION['role']           = $user['role'];

        // Enregistrer aussi la session en base de données (Model User)
        $this->userModel->saveSession(
            session_id(),
            $user['id_utilisateur'],
            $user['role']
        );

        // Rediriger selon le rôle
        $destinations = [
            'etudiant'      => 'index.php?route=student/home',
            'etablissement' => 'index.php?route=institution/home',
        ];
        $url = $destinations[$user['role']] ?? 'index.php';
        header("Location: $url");
        exit();
    }

    // ─────────────────────────────────────────────
    // Déconnexion
    // ─────────────────────────────────────────────

    /**
     * Déconnecte l'utilisateur : supprime la session PHP et la ligne en base.
     */
    public function logout(): void
    {
        // Supprimer la session de la base de données
        $this->userModel->deleteSession(session_id());

        // Vider toutes les variables de session PHP
        $_SESSION = [];

        // Supprimer le cookie de session dans le navigateur
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',       // Nom du cookie, valeur vide
                time() - 42000,          // Date d'expiration dans le passé -> suppression
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy(); // Détruire les données côté serveur
        header("Location: index.php");
        exit();
    }

    // ─────────────────────────────────────────────
    // Inscription — Choix du type de compte
    // ─────────────────────────────────────────────

    /**
     * Affiche la page de choix : étudiant ou établissement.
     */
    public function register(): void
    {
        redirect_if_logged();
        $this->render('layouts/header');
        $this->render('auth/register');
        $this->render('layouts/footer');
    }

    // ─────────────────────────────────────────────
    // Inscription étudiant
    // ─────────────────────────────────────────────

    /**
     * GET  -> affiche le formulaire d'inscription étudiant
     * POST -> crée le compte
     */
    public function registerStudent(): void
    {
        redirect_if_logged();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processStudentRegistration();
            return;
        }

        $this->render('layouts/header');
        $this->render('auth/student_register');
        $this->render('layouts/footer');
    }

    /**
     * Logique de création d'un compte étudiant.
     */
    private function processStudentRegistration(): void
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $lastName  = trim(htmlspecialchars($_POST['nom']    ?? ''));
        $firstName = trim(htmlspecialchars($_POST['prenom'] ?? ''));
        $series    = trim($_POST['serie_bac'] ?? '');

        $validSeries = ['A', 'C', 'D', 'L', 'OSE', 'S'];

        // Validation des champs
        if (!$email) {
            set_flash('error', 'Adresse email invalide');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
        if (strlen($password) < 8) {
            set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
        if ($password !== $confirm) {
            set_flash('error', 'Les mots de passe ne correspondent pas.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
        if (empty($lastName) || empty($firstName)) {
            set_flash('error', 'Nom et prénom sont obligatoires.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
        if (!in_array($series, $validSeries, true)) {
            // in_array avec true = comparaison stricte (type + valeur)
            set_flash('error', 'Série de baccalauréat invalide.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
        if ($this->userModel->emailExists($email)) {
            set_flash('error', 'Cette adresse email est déjà utilisée.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }

        try {
            // Toutes les opérations sont regroupées dans une transaction :
            // soit tout réussit, soit tout est annulé (cohérence des données)
            $this->pdo->beginTransaction();

            // 1. Créer l'utilisateur (compte de connexion)
            $userId = $this->userModel->create($email, $password, 'etudiant');

            // 2. Créer le profil étudiant lié à cet utilisateur
            $studentModel = new Student($this->pdo);
            $studentId    = $studentModel->create($lastName, $firstName, $userId);

            // 3. Créer un diplôme vierge (sera complété plus tard dans le profil)
            $degreeModel = new Degree($this->pdo);
            $degreeId    = $degreeModel->createBlank($studentId);

            // 4. Créer l'entrée bac liée avec la série choisie
            $degreeModel->createBlankExam($series, $degreeId);

            $this->pdo->commit();

            // Connexion automatique après inscription
            session_regenerate_id(true);
            $_SESSION['id_utilisateur'] = $userId;
            $_SESSION['role']           = 'etudiant';
            $this->userModel->saveSession(session_id(), $userId, 'etudiant');

            set_flash('success', 'Bienvenue sur Mpandova ! Votre compte a été créé.');
            header("Location: index.php?route=student/home");
            exit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur inscription étudiant : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=auth/student/register");
            exit();
        }
    }

    // ─────────────────────────────────────────────
    // Inscription établissement
    // ─────────────────────────────────────────────

    /**
     * GET  -> affiche le formulaire d'inscription établissement
     * POST -> crée le compte
     */
    public function registerInstitution(): void
    {
        redirect_if_logged();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->processInstitutionRegistration();
            return;
        }

        $this->render('layouts/header');
        $this->render('auth/register_etablissement');
        $this->render('layouts/footer');
    }

    /**
     * Logique de création d'un compte établissement.
     */
    private function processInstitutionRegistration(): void
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $name     = trim(htmlspecialchars($_POST['nom']  ?? ''));
        $type     = trim($_POST['type'] ?? '');

        // Validation des champs
        if (!$email) {
            set_flash('error', 'Adresse email invalide.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        if (strlen($password) < 8) {
            set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        if ($password !== $confirm) {
            set_flash('error', 'Les mots de passe ne correspondent pas.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        if (empty($name)) {
            set_flash('error', "Le nom de l'établissement est obligatoire.");
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        // Utilise Institution::validTypes() -- source unique de vérité
        if (!in_array($type, Institution::validTypes(), true)) {
            set_flash('error', "Type d'établissement invalide.");
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        if ($this->userModel->emailExists($email)) {
            set_flash('error', 'Cette adresse email est déjà utilisée.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            // 1. Créer l'utilisateur (compte de connexion)
            $userId = $this->userModel->create($email, $password, 'etablissement');

            // 2. Créer le profil établissement lié à cet utilisateur
            $institutionModel = new Institution($this->pdo);
            $institutionModel->create($name, $type, $userId);

            $this->pdo->commit();

            // Connexion automatique après inscription
            session_regenerate_id(true);
            $_SESSION['id_utilisateur'] = $userId;
            $_SESSION['role']           = 'etablissement';
            $this->userModel->saveSession(session_id(), $userId, 'etablissement');

            set_flash('success', 'Bienvenue sur Mpandova ! Votre compte a été créé.');
            header("Location: index.php?route=etablissement/accueil");
            exit();
        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur inscription établissement : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=auth/register-etablissement");
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
