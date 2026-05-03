<?php

    require_once("../config/auth.php");
    check_auth();
    check_role("etudiant");
?>
<h1>Bienvenue Etudiant</h1>
<a href="logout.php">Logout</a>