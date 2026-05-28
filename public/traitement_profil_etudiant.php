<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etudiant");

function calculerMention(?float $moyenne): ?string {
    if ($moyenne === null) {
        return null;
    }
    if ($moyenne >= 16) {
        return 'Très bien';
    }
    if ($moyenne >= 14) {
        return 'Bien';
    }
    if ($moyenne >= 12) {
        return 'Assez bien';
    }
    if ($moyenne >= 10) {
        return 'Passable';
    }
    return null;
}

//Recuperer l'etudiant 
$stmt=$pdo->prepare("SELECT * FROM etudiant WHERE id_utilisateur=?");
$stmt->execute([$_SESSION['id_utilisateur']]);
$etudiant=$stmt->fetch();

if (!$etudiant) {
    set_flash('error', 'Profil introuvable.');
    header("Location: accueil_etudiant.php");
    exit();
}

//Validation 
$nom   =trim(htmlspecialchars($_POST['nom']    ?? ''));
$prenom=trim(htmlspecialchars($_POST['prenom'] ?? ''));
$ddn   =$_POST['date_de_naissance'] ?? '';
$serie =trim($_POST['serie_bac']  ?? '');
$annee =(int) ($_POST['annee_bac']   ?? 0);
$moyenne_bac=(float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);

$series_valides=['A', 'C', 'D', 'L', 'OSE', 'S'];

if (empty($nom) || empty($prenom)) {
    set_flash('error', 'Nom et prénom sont obligatoires.');
    header("Location: profil_etudiant.php");
    exit();
}

if (!empty($serie) && !in_array($serie, $series_valides, true)) {
    set_flash('error', 'Série de baccalauréat invalide.');
    header("Location: profil_etudiant.php");
    exit();
}

if (!empty($ddn) && !DateTime::createFromFormat('Y-m-d', $ddn)) {
    set_flash('error', 'Date de naissance invalide.');
    header("Location: profil_etudiant.php");
    exit();
}

//Mot de passe (optionnel) 
$password        =$_POST['password']         ?? '';
$password_confirm=$_POST['password_confirm'] ?? '';

if (!empty($password)) {
    if (strlen($password) < 8) {
        set_flash('error', 'Le mot de passe doit contenir au moins 8 caractères.');
        header("Location: profil_etudiant.php");
        exit();
    }
    if ($password !== $password_confirm) {
        set_flash('error', 'Les mots de passe ne correspondent pas.');
        header("Location: profil_etudiant.php");
        exit();
    }
}

try {
    // Mise a jour etudiant 
    $stmt=$pdo->prepare("
        UPDATE etudiant SET nom=?, prenom=?, date_de_naissance=?
        WHERE id_etudiant=?
    ");
    $stmt->execute([
        $nom,
        $prenom,
        !empty($ddn) ? $ddn : null,
        $etudiant['id_etudiant'],
    ]);

    // Mise a jour diplome + bac 
    if (!empty($serie) && !empty($annee)) {
        //Vérifier si un diplome/bac existe déja
        $stmt=$pdo->prepare("SELECT d.id_diplome, b.id_bac FROM diplome d LEFT JOIN bac b ON b.id_diplome=d.id_diplome WHERE d.id_etudiant=? LIMIT 1");
        $stmt->execute([$etudiant['id_etudiant']]);
        $diplome=$stmt->fetch();
        $mention =calculerMention($moyenne_bac);

        if ($diplome) {
            //Mettre a jour le bac existant
            $stmt=$pdo->prepare("UPDATE diplome SET annee_obtention=? WHERE id_diplome=?");
            $stmt->execute([$annee>0 ? $annee : null, $diplome['id_diplome']]);
            
            $stmt=$pdo->prepare("UPDATE bac SET serie=?, moyenne=?, mention=? WHERE id_bac=?");
            $stmt->execute([$serie, $moyenne_bac, $mention, $diplome['id_bac']]);    
        }
    }

    //Mise a jour mot de passe 
    if (!empty($password)) {
        $hash=password_hash($password, PASSWORD_DEFAULT);
        $stmt=$pdo->prepare("UPDATE user SET password=? WHERE id_user=?");
        $stmt->execute([$hash, $_SESSION['id_user']]);
    }

    set_flash('success', 'Profil mis a jour avec succès.');
    header("Location: profil_etudiant.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur mise a jour profil étudiant : " . $e->getMessage());
    set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
    header("Location: profil_etudiant.php");
    exit();
}