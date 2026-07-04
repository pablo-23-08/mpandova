<?php
// Démarrage de session sécurisé, exécuté une seule fois même si le fichier est inclus plusieurs fois
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,        // Le cookie expire à la fermeture du navigateur
        'path'     => '/',      // Valable pour tout le site
        'secure'   => false,    // Mettre true en production (HTTPS obligatoire)
        'httponly' => true,     // Interdit l'accès au cookie depuis JavaScript (protection XSS)
        'samesite' => 'Lax',    // Protection CSRF : le cookie n'est pas envoyé depuis un autre site
    ]);
    session_start();
}

// ─────────────────────────────────────────────
// FONCTIONS D'AUTHENTIFICATION
// ─────────────────────────────────────────────

/**
 * Vérifie que l'utilisateur est connecté ET que sa session existe en base.
 * Redirige vers l'accueil si ce n'est pas le cas.
 */
function check_auth(): void
{
    // Si la clé de session n'existe pas → l'utilisateur n'est pas connecté
    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location: index.php");
        exit();
    }

    // Double vérification : la session existe-t-elle aussi côté base de données ?
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM session WHERE id_session = :sid');
    $stmt->execute([':sid' => session_id()]);
    $sessionRow = $stmt->fetch();

    // Si la ligne n'existe pas ou que l'ID ne correspond pas → session invalide
    if (!$sessionRow || $sessionRow['id_utilisateur'] !== $_SESSION['id_utilisateur']) {
        header('Location: index.php');
        exit();
    }

    // Mise à jour du timestamp d'activité pour prolonger la session
    $stmt = $pdo->prepare('UPDATE session SET initial = :time WHERE id_session = :sid');
    $stmt->execute([':time' => time(), ':sid' => session_id()]);
}

/**
 * Vérifie que l'utilisateur est connecté ET qu'il a le bon rôle.
 * Exemple : check_role('etudiant') bloque l'accès à un établissement.
 */
function check_role(string $role): void
{
    check_auth(); // Appelle d'abord check_auth() pour vérifier la connexion
    if ($_SESSION['role'] !== $role) {
        header("Location: index.php");
        exit();
    }
}

/**
 * Redirige l'utilisateur déjà connecté vers son tableau de bord.
 * Utilisé sur les pages login/register pour éviter d'y accéder quand déjà connecté.
 */
function redirect_if_logged(): void
{
    if (!isset($_SESSION['id_utilisateur'])) {
        return; // Pas connecté → continuer normalement
    }

    // Vérifier aussi la validité de la session en base
    global $pdo;
    $stmt = $pdo->prepare('SELECT * FROM session WHERE id_session = :sid');
    $stmt->execute([':sid' => session_id()]);
    $sessionRow = $stmt->fetch();

    if (!$sessionRow || $sessionRow['id_utilisateur'] !== $_SESSION['id_utilisateur']) {
        // Session invalide : nettoyer proprement
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'], $params['secure'], $params['httponly']
            );
        }
        @session_destroy();
        return;
    }

    // Session valide → mettre à jour l'activité et rediriger selon le rôle
    $stmt = $pdo->prepare('UPDATE session SET initial = :time WHERE id_session = :sid');
    $stmt->execute([':time' => time(), ':sid' => session_id()]);

    $destinations = [
        'etudiant'      => '?route=etudiant/accueil',
        'etablissement' => '?route=etablissement/accueil',
    ];

    $role = $_SESSION['role'] ?? '';
    $url  = $destinations[$role] ?? '?route=home';

    header("Location: $url");
    exit();
}

// ─────────────────────────────────────────────
// MESSAGES FLASH
// ─────────────────────────────────────────────

/**
 * Enregistre un message temporaire en session.
 * Il sera affiché une seule fois puis supprimé.
 * @param string $type    'success' | 'error' | 'info'
 * @param string $message Le texte à afficher
 */
function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

/**
 * Récupère le message flash et le supprime de la session.
 * @return array|null Tableau ['type' => ..., 'message' => ...] ou null si aucun message
 */
function get_flash(): ?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']); // Supprimer pour qu'il ne s'affiche qu'une fois
    return $flash;
}