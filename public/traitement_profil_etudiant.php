<?php
require_once "../config/bootstrap.php";
check_auth();
check_role("etudiant");
verify_csrf();

// ─── Récupérer l'étudiant ─────────────────────────────────────────────────────
$stmt = $pdo->prepare("SELECT * FROM etudiant WHERE id_user = ?");
$stmt->execute([$_SESSION['id_user']]);
$etudiant = $stmt->fetch();

if (!$etudiant) {
    set_flash('error', 'Profil introuvable.');
    header("Location: accueil_etudiant.php");
    exit();
}

// ─── Validation ───────────────────────────────────────────────────────────────
$nom    = trim(htmlspecialchars($_POST['nom']    ?? ''));
$prenom = trim(htmlspecialchars($_POST['prenom'] ?? ''));
$ddn    = $_POST['date_de_naissance'] ?? '';
$serie  = trim($_POST['serie_bac']  ?? '');
$annee  = (int) ($_POST['annee_bac']   ?? 0);
$moyenne = (float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);

$series_valides = ['A', 'C', 'D', 'L', 'OSE', 'S'];

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

// ─── Mot de passe (optionnel) ─────────────────────────────────────────────────
$password         = $_POST['password']         ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';

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
    // ─── Mise à jour etudiant ─────────────────────────────────────────────────
    $stmt = $pdo->prepare("
        UPDATE etudiant SET nom = ?, prenom = ?, date_de_naissance = ?, serie_bac = ?
        WHERE id_etudiant = ?
    ");
    $stmt->execute([
        $nom,
        $prenom,
        !empty($ddn) ? $ddn : null,
        !empty($serie) ? $serie : $etudiant['serie_bac'],
        $etudiant['id_etudiant'],
    ]);

    // ─── Mise à jour diplome + bac ────────────────────────────────────────────
    if (!empty($serie) && !empty($annee)) {
        // Vérifier si un diplome/bac existe déjà
        $stmt = $pdo->prepare("SELECT d.id_diplome, d.id_bac FROM diplome d WHERE d.id_etudiant = ?");
        $stmt->execute([$etudiant['id_etudiant']]);
        $diplome = $stmt->fetch();

        if ($diplome) {
            // Mettre à jour le bac existant
            $stmt = $pdo->prepare("UPDATE bac SET serie = ?, annee = ?, moyenne = ? WHERE id_bac = ?");
            $stmt->execute([$serie, $annee, $moyenne > 0 ? $moyenne : null, $diplome['id_bac']]);
        } else {
            // Créer bac + diplome
            $stmt = $pdo->prepare("INSERT INTO bac(serie, annee, moyenne) VALUES(?, ?, ?)");
            $stmt->execute([$serie, $annee, $moyenne > 0 ? $moyenne : null]);
            $id_bac = $pdo->lastInsertId();

            $stmt = $pdo->prepare("INSERT INTO diplome(id_etudiant, id_bac, annee) VALUES(?, ?, ?)");
            $stmt->execute([$etudiant['id_etudiant'], $id_bac, $annee]);
        }
    }

    // ─── Mise à jour mot de passe ─────────────────────────────────────────────
    if (!empty($password)) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("UPDATE user SET password = ? WHERE id_user = ?");
        $stmt->execute([$hash, $_SESSION['id_user']]);
    }

    set_flash('success', 'Profil mis à jour avec succès.');
    header("Location: profil_etudiant.php");
    exit();

} catch (PDOException $e) {
    error_log("Erreur mise à jour profil étudiant : " . $e->getMessage());
    set_flash('error', 'Une erreur est survenue. Veuillez réessayer.');
    header("Location: profil_etudiant.php");
    exit();
}