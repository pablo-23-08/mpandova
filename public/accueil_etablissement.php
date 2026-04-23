<?php
session_start();

if(!isset($_SESSION['id_user'])){
    header("Location: login.php");
    exit();
}
?>
<h1>Bienvenue Etablissement</h1>
<a href="logout.php">Logout</a>