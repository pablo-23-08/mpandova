<?php
    require_once "../config/bootstrap.php";

//Validation 
    $email   =filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password=$_POST['password'] ?? '';

    if (!$email||empty($password)) {
        set_flash('error', 'Veuillez remplir tous les champs correctement.');
        header("Location: login.php");
        exit();
    }

    //Authentification 
    $stmt=$pdo->prepare("SELECT * FROM utilisateur WHERE email=?");
    $stmt->execute([$email]);
    $utilisateur=$stmt->fetch();

    if (!$utilisateur||!password_verify($password, $utilisateur['mot_de_passe_hash'])) {
        set_flash('error', 'Email ou mot de passe incorrect.');
        header("Location: login.php");
        exit();
    }

//Creation de session 

    //Regenerer l'ID de session pour prevenir la fixation de session(hacker obtient une id_session d'un utilisateur connecté)
    session_regenerate_id(true);

    $_SESSION['id_utilisateur']=$utilisateur['id_utilisateur'];
    $_SESSION['role']   =$utilisateur['role'];

    //Redirection selon le rôle 
    $destinations=[
        'etudiant'     =>'accueil_etudiant.php',
        'etablissement'=>'accueil_etablissement.php',
    ];

    $url=$destinations[$utilisateur['role']] ?? 'index.php';
    header("Location: $url");
    exit();