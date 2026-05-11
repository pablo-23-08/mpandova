<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etablissement");
verify_csrf();

//Recuperer l'etablissement 
$stmt=$pdo->prepare("SELECT * FROM etablissement WHERE id_user=?");
$stmt->execute([$_SESSION['id_user']]);
$etablissement=$stmt->fetch();

if (!$etablissement) {
    set_flash('error', 'Profil introuvable.');
    header("Location: accueil_etablissement.php");
    exit();
}

//Validation 
$nom    =trim(htmlspecialchars($_POST['nom']     ?? ''));
$type   =trim($_POST['type']    ?? '');
$site_web= trim($_POST['site_web'] ?? '');
$ville  =trim(htmlspecialchars($_POST['ville']   ?? ''));
$adresse=trim(htmlspecialchars($_POST['adresse'] ?? ''));

$types_valides=['universite', 'grande_ecole', 'institut_prive', 'lycee_technique', 'autre'];

if (empty($nom)) {
    set_flash('error', "Le nom de l'etablissement est obligatoire.");
    header("Location: profil_etablissement.php");
    exit();
}

if (!in_array($type, $types_valides, true)) {
    set_flash('error', "Type d'etablissement invalide.");
    header("Location: profil_etablissement.php");
    exit();
}

if (!empty($site_web) && !filter_var($site_web, FILTER_VALIDATE_URL)) {
    set_flash('error', "L'URL du site web est invalide.");
    header("Location: profil_etablissement.php");
    exit();
}

//Mot de passe (optionnel) 
$password        =$_POST['password']         ?? '';
$password_confirm=$_POST['password_confirm'] ?? '';

if (!empty($password)) {
    if (strlen($password) < 8) {
        set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
        header("Location: profil_etablissement.php");
        exit();
    }
    if ($password !== $password_confirm) {
        set_flash('error', 'Les mots de passe ne correspondent pas.');
        header("Location: profil_etablissement.php");
        exit();
    }
}

try {
    //Mise a jour etablissement 
    $stmt=$pdo->prepare("UPDATE etablissement SET nom=?, type=?, site_web=? WHERE id_etablissement=?");
    $stmt->execute([
        $nom,
        $type,
        !empty($site_web) ? $site_web : null,
        $etablissement['id_etablissement'],
    ]);

    //Mise a jour / creation location 
    $stmt=$pdo->prepare("SELECT id_etablissement FROM location WHERE id_etablissement=?");
    $stmt->execute([$etablissement['id_etablissement']]);
    $loc=$stmt->fetch();

    if ($loc) {
        $stmt=$pdo->prepare("UPDATE location SET ville=?, adresse=? WHERE id_etablissement=?");
        $stmt->execute([$ville ?: null, $adresse ?: null, $etablissement['id_etablissement']]);
    } else {
        $stmt=$pdo->prepare("INSERT INTO location(id_etablissement, ville, adresse) VALUES(?, ?, ?)");
        $stmt->execute([$etablissement['id_etablissement'], $ville ?: null, $adresse ?: null]);
    }

    //Mise a jour mot de passe 
    if (!empty($password)) {
        $hash=password_hash($password, PASSWORD_DEFAULT);
        $stmt=$pdo->prepare("UPDATE user SET password=? WHERE id_user=?");
        $stmt->execute([$hash, $_SESSION['id_user']]);
    }

    set_flash('success', 'Profil mis a jour avec succès.');
    header("Location: profil_etablissement.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur mise a jour profil etablissement : " . $e->getMessage());
    set_flash('error', 'Une erreur est survenue. Veuillez reessayer.');
    header("Location: profil_etablissement.php");
    exit();
}