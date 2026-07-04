<?php
// ═══════════════════════════════════════════════
// FRONT CONTROLLER — Point d'entrée unique du site
// ═══════════════════════════════════════════════
//
// Toutes les URLs passent par ce fichier.
// Exemple : http://localhost:8000/public/index.php?route=auth/login
//
// En MVC, une seule porte d'entrée centralise le traitement
// de chaque requête, plutôt que d'avoir 20 fichiers PHP séparés.

// 1. Charger la configuration (connexion BDD + fonctions auth)
require_once __DIR__ . "/../config/bootstrap.php";

// 2. Charger le routeur qui va lire l'URL et déclencher le bon Controller
require_once __DIR__ . "/../routes.php";