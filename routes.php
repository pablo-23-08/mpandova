<?php
// ═══════════════════════════════════════════════
// ROUTEUR — Associe une URL à un Controller
// ═══════════════════════════════════════════════

require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/EtudiantController.php";
require_once __DIR__ . "/app/controllers/EtablissementController.php";
require_once __DIR__ . "/app/controllers/FiliereController.php";    // ← NOUVEAU

$route = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'home';

$routes = [
    // ── Page d'accueil publique ──
    'home'                                => ['AuthController',          'home'],

    // ── Authentification ──
    'auth/login'                          => ['AuthController',          'login'],
    'auth/logout'                         => ['AuthController',          'logout'],
    'auth/register'                       => ['AuthController',          'register'],
    'auth/register-etudiant'              => ['AuthController',          'registerEtudiant'],
    'auth/register-etablissement'         => ['AuthController',          'registerEtablissement'],

    // ── Espace étudiant ──
    'etudiant/accueil'                    => ['EtudiantController',      'accueil'],
    'etudiant/profil'                     => ['EtudiantController',      'profil'],
    'etudiant/etablissements'             => ['EtudiantController',      'etablissements'],      // ← NOUVEAU
    'etudiant/recommandations'            => ['EtudiantController',      'recommandations'],     // ← NOUVEAU
    'etudiant/candidatures'               => ['EtudiantController',      'candidatures'],        // ← NOUVEAU
    'etudiant/candidature-soumettre'      => ['EtudiantController',      'soumettreCandidature'], // ← NOUVEAU
    'etudiant/candidature-annuler'        => ['EtudiantController',      'annulerCandidature'],  // ← NOUVEAU

    // ── Espace établissement ──
    'etablissement/accueil'               => ['EtablissementController', 'accueil'],
    'etablissement/profil'                => ['EtablissementController', 'profil'],
    'etablissement/filieres'              => ['FiliereController',       'index'],               // ← NOUVEAU
    'etablissement/filiere-ajouter'       => ['FiliereController',       'ajouter'],             // ← NOUVEAU
    'etablissement/filiere-modifier'      => ['FiliereController',       'modifier'],            // ← NOUVEAU
    'etablissement/filiere-supprimer'     => ['FiliereController',       'supprimer'],           // ← NOUVEAU
    'etablissement/candidatures'          => ['EtablissementController', 'candidatures'],        // ← NOUVEAU
    'etablissement/candidature-traiter'   => ['EtablissementController', 'traiterCandidature'],  // ← NOUVEAU
];

if (isset($routes[$route])) {
    [$controllerName, $method] = $routes[$route];
    $controller = new $controllerName($pdo);
    $controller->$method();
} else {
    http_response_code(404);
    echo "<h1>404 - Page introuvable</h1>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
}