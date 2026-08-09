<?php
// ============================================================
// SCRIPT DE CRÉATION DU COMPTE ADMINISTRATEUR
// Usage unique — Supprimer ce fichier après utilisation !
// ------------------------------------------------------------
// Via navigateur : http://localhost:8000/setup/create_admin.php
// Via terminal   : php setup/create_admin.php
// ============================================================

// Sécurité minimale : autorisé uniquement depuis localhost ou CLI
if (PHP_SAPI !== 'cli'
    && !in_array($_SERVER['REMOTE_ADDR'] ?? '', ['127.0.0.1', '::1'], true)
) {
    http_response_code(403);
    die("Accès refusé. Ce script ne s'exécute que depuis localhost.");
}

// Charger la connexion à la base de données (variable $pdo)
require_once __DIR__ . '/../config/database.php';

// ── Paramètres du compte admin (modifiez si besoin) ──────────
$email    = 'admin@mpandova.mg';
$password = 'Admin.2026';
// ─────────────────────────────────────────────────────────────

// Vérifier si un admin existe déjà (évite les doublons)
$stmt = $pdo->prepare(
    "SELECT id_utilisateur FROM utilisateur WHERE role = 'admin' LIMIT 1"
);
$stmt->execute();

if ($stmt->fetch()) {
    echo "Un compte administrateur existe déjà. Rien à faire.\n";
    echo "Supprimez ce fichier.\n";
    exit;
}

// password_hash() applique bcrypt (PASSWORD_DEFAULT) : irréversible
$hash = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare(
    "INSERT INTO utilisateur (email, mot_de_passe_hash, role)
     VALUES (?, ?, 'admin')"
);
$stmt->execute([$email, $hash]);

echo "Compte administrateur créé avec succès !\n";
echo "Email       : {$email}\n";
echo "Mot de passe : {$password}\n";
echo "\n";
echo "Supprimez ce fichier maintenant : setup/create_admin.php\n";