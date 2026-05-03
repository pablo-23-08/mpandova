<?php
// Démarrage de session unique et sécurisé
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => false, // Mettre true en production (HTTPS)
        'httponly' => true,
        'samesite' => 'Strict',
    ]);
    session_start();
}

// ─── Authentification ────────────────────────────────────────────────────────

/** Redirige vers l'accueil si non connecté */
function check_auth(): void
{
    if (!isset($_SESSION['id_user'])) {
        header("Location: index.php");
        exit();
    }
}

/** Redirige si le rôle ne correspond pas */
function check_role(string $role): void
{
    check_auth();
    if ($_SESSION['role'] !== $role) {
        header("Location: index.php");
        exit();
    }
}

/** Redirige vers le dashboard si déjà connecté */
function redirect_if_logged(): void
{
    if (!isset($_SESSION['id_user'])) {
        return;
    }

    $destinations = [
        'etudiant'      => 'accueil_etudiant.php',
        'etablissement' => 'accueil_etablissement.php',
        'admin'         => 'accueil_admin.php',
    ];

    $role = $_SESSION['role'] ?? '';
    $url  = $destinations[$role] ?? 'index.php';

    header("Location: $url");
    exit();
}

// ─── CSRF ────────────────────────────────────────────────────────────────────

/** Génère (ou récupère) le token CSRF de session */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Affiche le champ hidden CSRF à placer dans chaque <form> */
function csrf_field(): void
{
    echo '<input type="hidden" name="csrf_token" value="' . csrf_token() . '">';
}

/** Vérifie le token CSRF — tue le script si invalide */
function verify_csrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(403);
        die("Requête invalide (CSRF).");
    }
}

// ─── Messages flash ───────────────────────────────────────────────────────────

/** Enregistre un message flash en session */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/** Récupère et supprime le message flash (retourne null si absent) */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}