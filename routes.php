<?php
// ═══════════════════════════════════════════════
// ROUTEUR — Associe une URL à un Controller
// ═══════════════════════════════════════════════
//
// L'URL contient un paramètre GET appelé "route".
// Exemple : ?route=auth/login  →  AuthController → méthode login()
// Exemple : ?route=etudiant/profil  →  EtudiantController → méthode profil()

// Charger les Controllers (ils seront disponibles dans la suite du script)
require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/EtudiantController.php";
require_once __DIR__ . "/app/controllers/EtablissementController.php";

// Lire le paramètre "route" dans l'URL. Si absent, afficher la page d'accueil.
// filter_input sécurise la lecture : récupère uniquement une chaîne alphanumérique + /
$route = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_SPECIAL_CHARS) ?? 'home';

// Table de routage : associe chaque route à [NomDuController, 'nomDeLaMéthode']
// La méthode sera appelée selon si la requête est GET ou POST
$routes = [
    // ── Page d'accueil publique ──
    'home'                      => ['AuthController',          'home'],

    // ── Authentification ──
    'auth/login'                => ['AuthController',          'login'],
    'auth/logout'               => ['AuthController',          'logout'],
    'auth/register'             => ['AuthController',          'register'],
    'auth/register-etudiant'    => ['AuthController',          'registerEtudiant'],
    'auth/register-etablissement' => ['AuthController',        'registerEtablissement'],

    // ── Espace étudiant ──
    'etudiant/accueil'          => ['EtudiantController',      'accueil'],
    'etudiant/profil'           => ['EtudiantController',      'profil'],

    // ── Espace établissement ──
    'etablissement/accueil'     => ['EtablissementController', 'accueil'],
    'etablissement/profil'      => ['EtablissementController', 'profil'],
];

// La route demandée existe-t-elle dans la table ?
if (isset($routes[$route])) {
    [$controllerName, $method] = $routes[$route];
    // Instancier le Controller correspondant
    // $pdo est disponible grâce à bootstrap.php chargé dans index.php
    $controller = new $controllerName($pdo);
    // Appeler la méthode correspondante
    $controller->$method();
} else {
    // Route inconnue → page 404
    http_response_code(404);
    echo "<h1>404 - Page introuvable</h1>";
    echo "<p><a href='index.php'>Retour à l'accueil</a></p>";
}