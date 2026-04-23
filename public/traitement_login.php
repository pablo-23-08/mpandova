<?php
session_start();
require "../config/database.php";

$email = $_POST['email'];
$password = $_POST['password'];

$sql = "SELECT * FROM user WHERE email=?";
$stmt = $pdo->prepare($sql);
$stmt->execute([$email]);

$user = $stmt->fetch(PDO::FETCH_ASSOC);

if($user && password_verify($password,$user['password'])){

    $_SESSION['id_user'] = $user['id_user'];
    $_SESSION['role'] = $user['role'];

    $id_user = $user['id_user'];

    // vérifier étudiant
    $sql = "SELECT * FROM etudiant WHERE id_user=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_user]);

    if($stmt->rowCount() > 0){
        header("Location: accueil_etudiant.php");
        exit();
    }

    // vérifier établissement
    $sql = "SELECT * FROM etablissement WHERE id_user=?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$id_user]);

    if($stmt->rowCount() > 0){
        header("Location: accueil_etablissement.php");
        exit();
    }

}else{
    echo "Email ou mot de passe incorrect";
}