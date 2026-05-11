<?php
    require_once "../config/bootstrap.php";
    verify_csrf();

//Validation 
    $email   =filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password=$_POST['password'] ?? '';

    if (!$email||empty($password)) {
        set_flash('error', 'Veuillez remplir tous les champs correctement.');
        header("Location: login.php");
        exit();
    }

    //Authentification 
    $stmt=$pdo->prepare("SELECT * FROM user WHERE email=?");
    $stmt->execute([$email]);
    $user=$stmt->fetch();

    if (!$user||!password_verify($password, $user['password'])) {
        set_flash('error', 'Email ou mot de passe incorrect.');
        header("Location: login.php");
        exit();
    }

//Creation de session 

    //Regenerer l'ID de session pour prevenir la fixation de session
    session_regenerate_id(true);

    $_SESSION['id_user']=$user['id_user'];
    $_SESSION['role']   =$user['role'];

    //Redirection selon le rôle 
    $destinations=[
        'etudiant'     =>'accueil_etudiant.php',
        'etablissement'=>'accueil_etablissement.php',
    ];

    $url=$destinations[$user['role']] ?? 'index.php';
    header("Location: $url");
    exit();