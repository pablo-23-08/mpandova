<?php
// ═══════════════════════════════════════════════
// ROUTEUR — Fait le lien entre l'URL (?route=...) et le Controller à exécuter
// ═══════════════════════════════════════════════
//
// Les chemins d'URL (clés du tableau) ne sont volontairement pas modifiés :
// ils font partie du contrat d'interface du site (liens déjà en place dans les vues)
// et ne doivent pas changer pour ne pas casser la navigation existante.
// Seuls les noms de Controllers/méthodes (le code) sont traduits en anglais.

// Charger les Controllers (noms de classes et de fichiers traduits en anglais)
require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/StudentController.php";
require_once __DIR__ . "/app/controllers/InstitutionController.php";
require_once __DIR__ . "/app/controllers/ProgramController.php";

// Lecture du paramètre ?route=... dans l'URL, "home" par défaut
$route = filter_input(INPUT_GET, 'route', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'home';

// Table de routage : 'chemin-url' => ['NomDeLaClasse', 'nomDeLaMethode']
$routes = [
    'home' => ['AuthController', 'home'],

    // - Authentification -
    'auth/login'                  => ['AuthController', 'login'],
    'auth/logout'                 => ['AuthController', 'logout'],
    'auth/register'               => ['AuthController', 'register'],
    'auth/student/register'       => ['AuthController', 'registerStudent'],
    'auth/institution/register'   => ['AuthController', 'registerInstitution'],

    // - Espace étudiant -
    'student/home'                 => ['StudentController', 'home'],
    'student/profile'              => ['StudentController', 'profile'],
    'student/institutions'         => ['StudentController', 'institutions'],
    'student/offer/details'         => ['StudentController', 'offerDetails'],          // ← NOUVEAU
    'student/recommendations'      => ['StudentController', 'recommendations'],
    'student/applications'         => ['StudentController', 'applications'],
    'student/application/submit'   => ['StudentController', 'submitApplication'],
    'student/application/cancel'   => ['StudentController', 'cancelApplication'],

    // - Espace établissement -
    'institution/home'                => ['InstitutionController', 'home'],
    'institution/profile'             => ['InstitutionController', 'profile'],
    'institution/programs'            => ['ProgramController', 'index'],
    'institution/program/create'      => ['ProgramController', 'create'],
    'institution/program/edit'        => ['ProgramController', 'edit'],
    'institution/program/delete'      => ['ProgramController', 'delete'],
    'institution/applications'        => ['InstitutionController', 'applications'],
    'institution/application/process' => ['InstitutionController', 'processApplication'],
];

if (isset($routes[$route])) {
    [$controllerName, $method] = $routes[$route];
    $controller = new $controllerName($pdo);
    $controller->$method();
} else {
    http_response_code(404);
    echo "<h1>404 - Page introuvable</h1>";
    echo "<a href='index.php'>Retour à l'accueil</a>";
}