<?php
require_once "../config/bootstrap.php";
verify_csrf();

// ─── Validation commune ───────────────────────────────────────────────────────

$role     = $_POST['role'] ?? '';
$email    = filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password = $_POST['password'] ?? '';

$roles_valides = ['etudiant', 'etablissement'];

if (!in_array($role, $roles_valides, true)) {
    set_flash('error', 'Rôle invalide.');
    header("Location: register.php");
    exit();
}

if (!$email) {
    set_flash('error', 'Adresse email invalide.');
    header("Location: register_$role.php");
    exit();
}

if (strlen($password) < 8) {
    set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
    header("Location: register_$role.php");
    exit();
}

// ─── Vérifier si l'email existe déjà ─────────────────────────────────────────

$stmt = $pdo->prepare("SELECT id_user FROM user WHERE email = ?");
$stmt->execute([$email]);

if ($stmt->fetch()) {
    set_flash('error', 'Cette adresse email est déjà utilisée.');
    header("Location: register_$role.php");
    exit();
}

// ─── Validation spécifique au rôle ───────────────────────────────────────────

if ($role === 'etudiant') {
    $nom      = trim(htmlspecialchars($_POST['nom']    ?? ''));
    $prenom   = trim(htmlspecialchars($_POST['prenom'] ?? ''));
    $serie    = trim($_POST['serie_bac'] ?? '');
    $series_valides = ['A', 'C', 'D', 'E', 'F', 'G'];

    if (empty($nom) || empty($prenom)) {
        set_flash('error', 'Nom et prénom sont obligatoires.');
        header("Location: register_etudiant.php");
        exit();
    }
    if (!in_array($serie, $series_valides, true)) {
        set_flash('error', 'Série de baccalauréat invalide.');
        header("Location: register_etudiant.php");
        exit();
    }
}

if ($role === 'etablissement') {
    $nom   = trim(htmlspecialchars($_POST['nom']  ?? ''));
    $type  = trim($_POST['type'] ?? '');
    $types_valides = ['universite', 'grande_ecole', 'institut_prive', 'lycee_technique', 'autre'];

    if (empty($nom)) {
        set_flash('error', 'Le nom de l\'établissement est obligatoire.');
        header("Location: register_etablissement.php");
        exit();
    }
    if (!in_array($type, $types_valides, true)) {
        set_flash('error', 'Type d\'établissement invalide.');
        header("Location: register_etablissement.php");
        exit();
    }
}

// ─── Insertion en base ────────────────────────────────────────────────────────

try {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    $stmt = $pdo->prepare("INSERT INTO user(email, password, role) VALUES(?, ?, ?)");
    $stmt->execute([$email, $password_hash, $role]);
    $id_user = $pdo->lastInsertId();

    if ($role === 'etudiant') {
        $stmt = $pdo->prepare("INSERT INTO etudiant(nom, prenom, serie_bac, id_user) VALUES(?, ?, ?, ?)");
        $stmt->execute([$nom, $prenom, $serie, $id_user]);
    }

    if ($role === 'etablissement') {
        $stmt = $pdo->prepare("INSERT INTO etablissement(nom, type, id_user) VALUES(?, ?, ?)");
        $stmt->execute([$nom, $type, $id_user]);
    }

    // ─── Création de session ──────────────────────────────────────────────────

    session_regenerate_id(true);
    $_SESSION['id_user'] = $id_user;
    $_SESSION['role']    = $role;

    set_flash('success', 'Bienvenue sur Mpandova ! Votre compte a été créé.');

    $destinations = [
        'etudiant'      => 'accueil_etudiant.php',
        'etablissement' => 'accueil_etablissement.php',
    ];
    header("Location: " . $destinations[$role]);
    exit();

} catch (PDOException $e) {
    error_log("Erreur inscription : " . $e->getMessage());
    set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
    header("Location: register_$role.php");
    exit();
}