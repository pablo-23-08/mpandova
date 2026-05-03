<?php
$host     = "localhost";
$dbname   = "mpandova";
$user     = "root";
$password = "";

try {
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password
    );
    $pdo->setAttribute(PDO::ATTR_ERRMODE,      PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,   false);
} catch (PDOException $e) {
    // Ne jamais afficher les détails en production
    error_log("Erreur connexion BDD : " . $e->getMessage());
    die("Une erreur est survenue. Veuillez réessayer plus tard.");
}