<?php
// ═══════════════════════════════════════════════
// CONTROLLER AuthController
// Gère : page d'accueil, connexion, déconnexion, inscription
// ═══════════════════════════════════════════════

// Charger les Models dont ce Controller a besoin
require_once __DIR__ . "/../models/Utilisateur.php";
require_once __DIR__ . "/../models/Etudiant.php";
require_once __DIR__ . "/../models/Diplome.php";
require_once __DIR__ . "/../models/Etablissement.php";

class AuthController
{
    private PDO $pdo;               // Connexion à la base de données
    private Utilisateur $utilisateurModel; // Instance du Model Utilisateur

    // Constructeur : injecte la connexion PDO et instancie les Models nécessaires
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->utilisateurModel = new Utilisateur($pdo);
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
     * GET  → affiche le formulaire de connexion
     * POST → traite le formulaire et connecte l'utilisateur
     */
    public function login(): void
    {
        redirect_if_logged();

        // Si la requête est un envoi de formulaire (POST) → traiter les données
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterLogin();
            return;
        }

        // Sinon (GET) → afficher le formulaire
        $this->render('layouts/header');
        $this->render('auth/login');
        $this->render('layouts/footer');
    }

    /**
     * Logique de traitement du formulaire de connexion.
     * Méthode privée : appelée uniquement par login() en cas de POST.
     */
    private function traiterLogin(): void
    {
        // filter_input sécurise la récupération : valide le format email
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password'] ?? '';

        // Validation des champs
        if (!$email || empty($password)) {
            set_flash('error', 'Veuillez remplir tous les champs correctement.');
            header("Location: index.php?route=auth/login");
            exit();
        }

        // Chercher l'utilisateur par email (Model Utilisateur)
        $utilisateur = $this->utilisateurModel->findByEmail($email);

        // Vérifier le mot de passe : password_verify compare le clair avec le hash stocké
        if (!$utilisateur || !password_verify($password, $utilisateur['mot_de_passe_hash'])) {
            set_flash('error', 'Email ou mot de passe incorrect.');
            header("Location: index.php?route=auth/login");
            exit();
        }

        // Succès → créer la session PHP
        session_regenerate_id(true); // Prévention de la fixation de session
        $_SESSION['id_utilisateur'] = $utilisateur['id_utilisateur'];
        $_SESSION['role']           = $utilisateur['role'];

        // Enregistrer aussi la session en base de données (Model Utilisateur)
        $this->utilisateurModel->enregistrerSession(
            session_id(),
            $utilisateur['id_utilisateur'],
            $utilisateur['role']
        );

        // Rediriger selon le rôle
        $destinations = [
            'etudiant'      => 'index.php?route=etudiant/accueil',
            'etablissement' => 'index.php?route=etablissement/accueil',
        ];
        $url = $destinations[$utilisateur['role']] ?? 'index.php';
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
        $this->utilisateurModel->supprimerSession(session_id());

        // Vider toutes les variables de session PHP
        $_SESSION = [];

        // Supprimer le cookie de session dans le navigateur
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(), '',       // Nom du cookie, valeur vide
                time() - 42000,          // Date d'expiration dans le passé → suppression
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
     * GET  → affiche le formulaire d'inscription étudiant
     * POST → crée le compte
     */
    public function registerEtudiant(): void
    {
        redirect_if_logged();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterRegisterEtudiant();
            return;
        }

        $this->render('layouts/header');
        $this->render('auth/register_etudiant');
        $this->render('layouts/footer');
    }

    /**
     * Logique de création d'un compte étudiant.
     */
    private function traiterRegisterEtudiant(): void
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $nom      = trim(htmlspecialchars($_POST['nom']    ?? ''));
        $prenom   = trim(htmlspecialchars($_POST['prenom'] ?? ''));
        $serie    = trim($_POST['serie_bac'] ?? '');

        $seriesValides = ['A', 'C', 'D', 'L', 'OSE', 'S'];

        // Validation des champs
        if (!$email) {
            set_flash('error', 'Adresse email invalide.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }
        if (strlen($password) < 8) {
            set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }
        if ($password !== $confirm) {
            set_flash('error', 'Les mots de passe ne correspondent pas.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }
        if (empty($nom) || empty($prenom)) {
            set_flash('error', 'Nom et prénom sont obligatoires.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }
        if (!in_array($serie, $seriesValides, true)) {
            // in_array avec true = comparaison stricte (type + valeur)
            set_flash('error', 'Série de baccalauréat invalide.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }
        if ($this->utilisateurModel->emailExiste($email)) {
            set_flash('error', 'Cette adresse email est déjà utilisée.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }

        // Toutes les validations sont passées → insérer en base dans une transaction
        try {
            // begin() : si une des insertions échoue, tout est annulé (atomicité)
            $this->pdo->beginTransaction();

            // 1. Créer l'utilisateur (table utilisateur)
            $idUtilisateur = $this->utilisateurModel->creer($email, $password, 'etudiant');

            // 2. Créer le profil étudiant (table etudiant)
            $etudiantModel = new Etudiant($this->pdo);
            $idEtudiant    = $etudiantModel->creer($nom, $prenom, $idUtilisateur);

            // 3. Créer le diplôme vide et le bac avec la série (tables diplome + bac)
            $diplomeModel = new Diplome($this->pdo);
            $idDiplome    = $diplomeModel->creerVierge($idEtudiant);
            $diplomeModel->creerBacVierge($serie, $idDiplome);

            // Valider la transaction : toutes les insertions ont réussi
            $this->pdo->commit();

        } catch (PDOException $e) {
            // Annuler toutes les insertions si une erreur survient
            $this->pdo->rollBack();
            error_log("Erreur inscription étudiant : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=auth/register-etudiant");
            exit();
        }

        // Créer la session PHP après inscription réussie
        session_regenerate_id(true);
        $_SESSION['id_utilisateur'] = $idUtilisateur;
        $_SESSION['role']           = 'etudiant';
        $this->utilisateurModel->enregistrerSession(session_id(), $idUtilisateur, 'etudiant');

        set_flash('success', 'Bienvenue sur Mpandova ! Votre compte a été créé.');
        header("Location: index.php?route=etudiant/accueil");
        exit();
    }

    // ─────────────────────────────────────────────
    // Inscription établissement
    // ─────────────────────────────────────────────

    /**
     * GET  → affiche le formulaire d'inscription établissement
     * POST → crée le compte
     */
    public function registerEtablissement(): void
    {
        redirect_if_logged();

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $this->traiterRegisterEtablissement();
            return;
        }

        $this->render('layouts/header');
        $this->render('auth/register_etablissement');
        $this->render('layouts/footer');
    }

    /**
     * Logique de création d'un compte établissement.
     */
    private function traiterRegisterEtablissement(): void
    {
        $email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
        $password = $_POST['password']         ?? '';
        $confirm  = $_POST['password_confirm'] ?? '';
        $nom      = trim(htmlspecialchars($_POST['nom']  ?? ''));
        $type     = trim($_POST['type'] ?? '');

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
        if (empty($nom)) {
            set_flash('error', "Le nom de l'établissement est obligatoire.");
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        // Etablissement::typesValides() est la source unique de vérité pour les types
        if (!in_array($type, Etablissement::typesValides(), true)) {
            set_flash('error', "Type d'établissement invalide.");
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }
        if ($this->utilisateurModel->emailExiste($email)) {
            set_flash('error', 'Cette adresse email est déjà utilisée.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }

        try {
            $this->pdo->beginTransaction();

            $idUtilisateur      = $this->utilisateurModel->creer($email, $password, 'etablissement');
            $etablissementModel = new Etablissement($this->pdo);
            $etablissementModel->creer($nom, $type, $idUtilisateur);

            $this->pdo->commit();

        } catch (PDOException $e) {
            $this->pdo->rollBack();
            error_log("Erreur inscription établissement : " . $e->getMessage());
            set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
            header("Location: index.php?route=auth/register-etablissement");
            exit();
        }

        session_regenerate_id(true);
        $_SESSION['id_utilisateur'] = $idUtilisateur;
        $_SESSION['role']           = 'etablissement';
        $this->utilisateurModel->enregistrerSession(session_id(), $idUtilisateur, 'etablissement');

        set_flash('success', 'Bienvenue sur Mpandova ! Votre compte a été créé.');
        header("Location: index.php?route=etablissement/accueil");
        exit();
    }

    // ─────────────────────────────────────────────
    // Méthode utilitaire : rendu d'une vue
    // ─────────────────────────────────────────────

    /**
     * Inclut un fichier de vue.
     * Les variables passées en 2ème argument deviennent disponibles dans la vue.
     *
     * @param string $view   Chemin relatif depuis app/views/ (sans .php)
     * @param array  $data   Variables à injecter dans la vue (ex: ['nom' => 'Jean'])
     */
    protected function render(string $view, array $data = []): void
    {
        // extract() transforme ['nom' => 'Jean'] en variable $nom = 'Jean'
        // EXTR_SKIP : si une variable du même nom existe déjà, ne pas l'écraser
        extract($data, EXTR_SKIP);
        // Construire le chemin absolu vers le fichier de vue
        require __DIR__ . "/../views/{$view}.php";
    }
}