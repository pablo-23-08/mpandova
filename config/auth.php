<?php
session_start();

// vérifier si connecté
function check_auth(){
    if(!isset($_SESSION['id_user'])){
        header("Location: index.php");
        exit();
    }
}

// vérifier rôle
function check_role($role){
    if(!isset($_SESSION['role']) || $_SESSION['role'] != $role){
        header("Location: index.php");
        exit();
    }
}

// empêcher l'accès si déjà connecté
function redirect_if_logged(){
    if(isset($_SESSION['id_user'])){
        // redirection selon le rôle
        if($_SESSION['role'] == 'etudiant'){
            header("Location: accueil_etudiant.php");
        }
        elseif($_SESSION['role'] == 'etablissement'){
            header("Location: accueil_etablissement.php");
        }
        elseif($_SESSION['role'] == 'admin'){
            header("Location: accueil_admin.php");
        }
        exit();
    }
}