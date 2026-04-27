<?php
    session_start();
    require_once("../config/auth.php");
    check_auth();
    check_role("etablissement");
?>
<h1>Bienvenue Etablissement</h1>
<a href="logout.php">Logout</a>