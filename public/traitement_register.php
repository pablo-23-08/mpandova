<?php
session_start();
require "../config/database.php";

$email = $_POST['email'];
$password = password_hash($_POST['password'],PASSWORD_DEFAULT);
$role = $_POST['role'];

try{
    // insertion user
    $sql = "INSERT INTO user(email,password,role) VALUES(?,?,?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$email,$password,$role]);

    $id_user = $pdo->lastInsertId();

    if($role == "etudiant"){
        $sql = "INSERT INTO etudiant(nom,prenom,serie_bac,id_user) VALUES(?,?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nom'],
            $_POST['prenom'],
            $_POST['serie_bac'],
            $id_user
        ]);
    }

    if($role == "etablissement"){
        $sql = "INSERT INTO etablissement(nom,type,id_user) VALUES(?,?,?)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $_POST['nom'],
            $_POST['type'],
            $id_user
        ]);
    }

    // création session
    $_SESSION['id_user'] = $id_user;
    $_SESSION['role'] = $role;

    // redirection selon rôle
    if($role == "etudiant"){
        header("Location: accueil_etudiant.php");
    }else{
        header("Location: accueil_etablissement.php");
    }
    exit();
    
}catch(Exception $e){
    echo "Erreur : ".$e->getMessage();
}

