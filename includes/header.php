<?php
if(session_status()==PHP_SESSION_NONE){session_start();}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
<meta charset="UTF-8">
<title>Mpandova</title>
<link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>

<header class="header">
    <div class="container nav">
        <div class="logo">
            <img src="../assets/img/logo.png" alt="logo">
            <span>Mpandova</span>
        </div>

        <nav>
            <a href="index.php">Accueil</a>
            <?php if(isset($_SESSION['user'])){ ?>
                <a href="dashboard.php">Dashboard</a>
                <a href="logout.php">Logout</a>
            <?php } else { ?>
                <a href="login.php">Login</a>
                <a href="register.php">Register</a>
            <?php } ?>
        </nav>
    </div>
</header>

<main class="container">