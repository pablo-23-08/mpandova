<?php
session_start();

// vérifier si connecté
function check_auth(){
    if(!isset($_SESSION['id_user'])){
        header("Location: login.php");
        exit();
    }
}

// vérifier rôle
function check_role($role){
    if(!isset($_SESSION['role']) || $_SESSION['role'] != $role){
        header("Location: login.php");
        exit();
    }
}