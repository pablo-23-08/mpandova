<?php
// ═══════════════════════════════════════════════
// ROUTEUR — Fait le lien entre l'URL (?route=...) et le Controller à exécuter
// ═══════════════════════════════════════════════

require_once __DIR__ . "/app/controllers/AuthController.php";
require_once __DIR__ . "/app/controllers/StudentController.php";
require_once __DIR__ . "/app/controllers/InstitutionController.php";
require_once __DIR__ . "/app/controllers/ProgramController.php";
require_once __DIR__ . "/app/controllers/OutletController.php";
require_once __DIR__ . "/app/controllers/AdminController.php";

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
    'student/offer/details'        => ['StudentController', 'offerDetails'],
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
    'institution/program/outlets'         => ['OutletController', 'index'],
    'institution/program/outlets/store'   => ['OutletController', 'store'],
    'institution/program/outlets/edit'    => ['OutletController', 'edit'],
    'institution/program/outlets/delete'  => ['OutletController', 'delete'],
    'institution/applications'        => ['InstitutionController', 'applications'],
    'institution/application/process' => ['InstitutionController', 'processApplication'],

    // ─── Espace administrateur ───────────────────

    // 1. Tableau de bord
    'admin/home'                   => ['AdminController', 'home'],

    // 2. Gestion des utilisateurs
    'admin/users'                  => ['AdminController', 'users'],
    'admin/user/view'              => ['AdminController', 'viewUser'],
    'admin/user/add'               => ['AdminController', 'addUser'],
    'admin/user/edit'              => ['AdminController', 'editUser'],
    'admin/user/delete'            => ['AdminController', 'deleteUser'],

    // 3. Gestion des établissements
    'admin/institutions'           => ['AdminController', 'institutions'],
    'admin/institution/view'       => ['AdminController', 'viewInstitution'],
    'admin/institution/edit'       => ['AdminController', 'editInstitution'],
    'admin/institution/delete'     => ['AdminController', 'deleteInstitution'],

    // 4. Gestion des formations (offres de filières)
    'admin/formations'             => ['AdminController', 'formations'],
    'admin/formation/view'         => ['AdminController', 'viewFormation'],
    'admin/formation/edit'         => ['AdminController', 'editFormation'],
    'admin/formation/delete'       => ['AdminController', 'deleteFormation'],

    // Catalogue de filières (référentiel)
    'admin/programs'               => ['AdminController', 'programs'],
    'admin/program/create'         => ['AdminController', 'createProgram'],
    'admin/program/edit'           => ['AdminController', 'editProgram'],
    'admin/program/delete'         => ['AdminController', 'deleteProgram'],

    // 5. Gestion des candidatures
    'admin/applications'           => ['AdminController', 'applications'],
    'admin/application/view'       => ['AdminController', 'viewApplication'],
];

if (isset($routes[$route])) {
    [$controllerName, $method] = $routes[$route];
    $controller = new $controllerName($pdo);
    $controller->$method();
} else {
    http_response_code(404);
    echo "<!DOCTYPE html><html lang='fr'><head><meta charset='UTF-8'><title>404 — Mpandova</title></head>";
    echo "<body style='font-family:sans-serif;text-align:center;padding:60px'>";
    echo "<h1>404 — Page introuvable</h1>";
    echo "<a href='index.php'>Retour à l'accueil</a>";
    echo "</body></html>";
}
