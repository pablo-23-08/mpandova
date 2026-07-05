<?php
// Paramètres de connexion à la base de données MySQL
$host     = "localhost";   // Adresse du serveur MySQL (ici, machine locale)
$dbname   = "mpandova_db";    // Nom de la base de données
$user     = "root";        // Utilisateur MySQL (administrateur par défaut en dev)
$password = "";            // Mot de passe vide en développement local

// $host     = "mysql-mpandova.alwaysdata.net";   // Adresse du serveur MySQL en ligne (Alwaysdata)
// $dbname   = "mpandova_db";    // Nom de la base de données
// $user     = "mpandova";        // Utilisateur MySQL
// $password = "Mpandova.2026";            // Mot de passe

try {
    // Création de l'objet PDO : le "canal" de communication avec MySQL
    // Le DSN (Data Source Name) indique : moteur=mysql, serveur, base, encodage UTF-8
    $pdo = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $password
    );

    // En cas d'erreur SQL, PHP lance une exception (attrapable avec try/catch)
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Les résultats des SELECT seront des tableaux associatifs : $row['nom']
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    // Utilise les vraies requêtes préparées MySQL (plus sécurisé contre les injections)
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);

} catch (PDOException $e) {
    // En cas d'échec, écrire l'erreur dans les logs serveur (invisible pour l'utilisateur)
    error_log("Erreur connexion BDD : " . $e->getMessage());
    // Stopper le script avec un message sobre (ne révèle aucune info sensible)
    die("Erreur de connexion à la base de données.");
}
