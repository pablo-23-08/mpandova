<?php
// Point d'entrée de configuration.
// Ce fichier est inclus en premier dans chaque requête via public/index.php.
// Il charge la connexion à la base ($pdo) et toutes les fonctions d'auth.

require_once __DIR__ . "/database.php";  // Crée la variable $pdo
require_once __DIR__ . "/auth.php";      // Déclare les fonctions check_auth(), set_flash(), etc.