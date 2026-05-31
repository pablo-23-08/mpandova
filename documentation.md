# Analyse complète du projet Mpandova — Guide pour débutants

> **Mpandova** est une application web d'orientation académique à Madagascar. Elle aide les étudiants à trouver une filière et un établissement après le bac.

---

## Vue d'ensemble du projet

### Qu'est-ce que ce projet ?

Mpandova est un site web construit avec :

- **PHP** : le langage côté serveur (ce qui se passe "dans les coulisses")
- **MySQL** : la base de données (où sont stockées les informations)
- **HTML/CSS** : ce que l'utilisateur voit dans son navigateur
- **Tailwind CSS** : un outil pour styliser rapidement les pages
- **PDO** : une façon sécurisée de parler à MySQL depuis PHP

### Architecture du projet (structure des dossiers)

```
mpandova/
├── assets/
│   └── css/
│       ├── input.css       ← fichier source Tailwind
│       └── output.css      ← CSS généré (non versionné)
├── config/
│   ├── database.php        ← connexion à la base de données
│   ├── auth.php            ← fonctions de sécurité et session
│   └── bootstrap.php       ← point d'entrée de configuration
├── app/
│   └── views/
│       ├── home.php        ← contenu de la page d'accueil
│       └── layouts/
│           ├── header.php  ← en-tête commun à toutes les pages
│           └── footer.php  ← pied de page commun
├── public/
│   ├── index.php                        ← page d'accueil
│   ├── login.php                        ← page de connexion
│   ├── register.php                     ← choix du type de compte
│   ├── register_etudiant.php            ← inscription étudiant
│   ├── register_etablissement.php       ← inscription établissement
│   ├── traitement_login.php             ← logique de connexion
│   ├── traitement_register.php          ← logique d'inscription
│   ├── accueil_etudiant.php             ← tableau de bord étudiant
│   ├── accueil_etablissement.php        ← tableau de bord établissement
│   ├── profil_etudiant.php              ← modifier profil étudiant
│   ├── profil_etablissement.php         ← modifier profil établissement
│   ├── traitement_profil_etudiant.php   ← logique de mise à jour étudiant
│   ├── traitement_profil_etablissement.php ← logique de mise à jour établissement
│   └── logout.php                       ← déconnexion
├── mpandova.sql            ← script de création de la base de données
└── package.json            ← configuration Node.js (pour Tailwind)
```

### Comment les fichiers interagissent ?

```
Navigateur → public/index.php
                ↓
          config/bootstrap.php (charge database.php + auth.php)
                ↓
          app/views/layouts/header.php (affiche l'en-tête)
                ↓
          app/views/home.php (affiche le contenu)
                ↓
          app/views/layouts/footer.php (affiche le pied de page)
```

---

# Fichier 1 : `mpandova.sql`
 
## 1. Rôle du fichier
 
Ce fichier est le **plan de la base de données**. Il définit toutes les tables qui vont stocker les données de l'application : utilisateurs, étudiants, établissements, filières, etc.
 
## 2. Vue d'ensemble
 
Un fichier SQL contient des **instructions pour créer une base de données**. On l'exécute une seule fois pour préparer MySQL à recevoir des données.
 
## 3. Explication ligne par ligne
 
---
 
**Ligne 1 :**
 
```sql
DROP DATABASE IF EXISTS mpandova;
```
 
- `DROP DATABASE` : supprime une base de données entière si elle existe déjà.
- `IF EXISTS` : évite une erreur si la base n'existe pas encore.
- Utile pendant le développement pour repartir de zéro sans erreur.
---
 
**Lignes 3-6 :**
 
```sql
CREATE DATABASE IF NOT EXISTS mpandova
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;
```
 
- `CREATE DATABASE` : crée une nouvelle base de données.
- `IF NOT EXISTS` : ne pas afficher d'erreur si elle existe déjà.
- `CHARACTER SET utf8mb4` : encodage qui supporte tous les caractères internationaux, emojis et accents français.
- `COLLATE utf8mb4_unicode_ci` : définit la façon de comparer et trier les textes. `ci` = "case-insensitive" (insensible à la casse).
---
 
**Ligne 8 :**
 
```sql
USE mpandova;
```
 
- Indique à MySQL que toutes les prochaines instructions concernent la base `mpandova`.
---
 
**Lignes 10-17 — Table `utilisateur` :**
 
```sql
CREATE TABLE utilisateur (
    id_utilisateur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('etudiant', 'etablissement') NOT NULL
);
```
 
- `CREATE TABLE utilisateur` : crée une table pour tous les comptes (étudiants et établissements).
- `id_utilisateur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY` : identifiant unique auto-incrémenté (1, 2, 3…).
- `email VARCHAR(100) NOT NULL UNIQUE` : email obligatoire et unique — deux comptes ne peuvent pas partager le même email.
- `mot_de_passe_hash VARCHAR(255) NOT NULL` : stocke le mot de passe **haché** (jamais en clair).
- `role ENUM('etudiant', 'etablissement') NOT NULL` : le rôle ne peut être que l'une de ces deux valeurs.
> ⚠️ **Important** : le champ s'appelle `mot_de_passe_hash` (et non `password`) et la table `utilisateur` (et non `user`). C'est une différence clé avec l'ancienne documentation.
 
---
 
**Lignes 19-32 — Table `etudiant` :**
 
```sql
CREATE TABLE etudiant (
    id_etudiant INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    prenom VARCHAR(100) NOT NULL,
    date_de_naissance DATE DEFAULT NULL,
    telephone VARCHAR(20) DEFAULT NULL,
    id_utilisateur INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_etudiant_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);
```
 
- `telephone VARCHAR(20) DEFAULT NULL` : nouveau champ optionnel par rapport à la version précédente.
- `id_utilisateur` : clé étrangère vers la table `utilisateur`. `UNIQUE` = un utilisateur ne peut être étudiant qu'une seule fois.
- `ON DELETE CASCADE` : si l'utilisateur est supprimé, son profil étudiant l'est aussi automatiquement.
---
 
**Lignes 34-50 — Table `etablissement` :**
 
```sql
CREATE TABLE etablissement (
    id_etablissement INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    type ENUM(
        'universite_publique',
        'universite_privee',
        'grande_ecole',
        'institut',
        'autre'
    ) NOT NULL,
    site_web VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    id_utilisateur INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_etablissement_utilisateur
        FOREIGN KEY (id_utilisateur)
        REFERENCES utilisateur(id_utilisateur)
        ON DELETE CASCADE
);
```
 
- Les types valides sont : `universite_publique`, `universite_privee`, `grande_ecole`, `institut`, `autre`.
- `description TEXT` : champ texte long optionnel, nouveau par rapport à l'ancienne version.
- `site_web VARCHAR(255)` : 255 caractères (au lieu de 100) pour les URLs longues.
---
 
**Lignes 52-65 — Table `localisation` :**
 
```sql
CREATE TABLE localisation (
    id_localisation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    ville VARCHAR(100) DEFAULT NULL,
    adresse VARCHAR(255) DEFAULT NULL,
    region VARCHAR(100) DEFAULT NULL,
    id_etablissement INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_localisation_etablissement
        FOREIGN KEY (id_etablissement)
        REFERENCES etablissement(id_etablissement)
        ON DELETE CASCADE
);
```
 
- Cette table remplace l'ancienne table `location`. Elle s'appelle maintenant `localisation`.
- Ajout d'un champ `region` pour préciser la région géographique.
- `id_localisation` est maintenant une clé primaire auto-incrémentée (différence avec l'ancienne version).
---
 
**Lignes 67-72 — Table `filiere` :**
 
```sql
CREATE TABLE filiere (
    id_filiere INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL
);
```
 
- Stocke les filières disponibles (ex: Informatique, Médecine, Droit…).
---
 
**Lignes 74-78 — Table `debouche` :**
 
```sql
CREATE TABLE debouche (
    id_debouche INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL
);
```
 
- Stocke les débouchés professionnels (ex: Développeur, Médecin…).
---
 
**Lignes 80-96 — Table `mener` :**
 
```sql
CREATE TABLE mener (
    id_filiere INT UNSIGNED NOT NULL,
    id_debouche INT UNSIGNED NOT NULL,
    niveau_etude VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_filiere, id_debouche),
    ...
);
```
 
- Table de liaison entre `filiere` et `debouche` : une filière mène vers plusieurs débouchés.
- `PRIMARY KEY (id_filiere, id_debouche)` : clé primaire composite — la combinaison des deux IDs est unique.
---
 
**Lignes 98-116 — Table `offre_filiere` :**
 
```sql
CREATE TABLE offre_filiere (
    id_offre_filiere INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    frais_scolarite DECIMAL(10,2) DEFAULT 0,
    place_disponible INT DEFAULT 0,
    duree_formation VARCHAR(100) DEFAULT NULL,
    id_etablissement INT UNSIGNED NOT NULL,
    id_filiere INT UNSIGNED NOT NULL,
    ...
);
```
 
- **Nouvelle table** : représente une filière telle que proposée par un établissement spécifique, avec ses propres frais et conditions.
- `DECIMAL(10,2)` : nombre décimal avec 10 chiffres au total et 2 après la virgule (ex: 1500000.00).
---
 
**Lignes 118-135 — Table `condition_acces` :**
 
```sql
CREATE TABLE condition_acces (
    id_condition_acces INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    diplome_requis VARCHAR(150) DEFAULT NULL,
    age_max INT DEFAULT NULL,
    serie_bac ENUM('A','C','D','L','OSE','S') DEFAULT NULL,
    annee_bac YEAR DEFAULT NULL,
    moyenne_bac DECIMAL(4,2) DEFAULT NULL,
    id_offre_filiere INT UNSIGNED NOT NULL UNIQUE,
    ...
);
```
 
- **Nouvelle table** : définit les conditions d'accès à une offre de filière.
- Remplace l'ancienne table `condition_admission`.
- `DECIMAL(4,2)` pour la moyenne (ex: 14.50).
---
 
**Lignes 137-149 — Table `diplome` :**
 
```sql
CREATE TABLE diplome (
    id_diplome INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    annee_obtention YEAR NULL,
    id_etudiant INT UNSIGNED NOT NULL,
    ...
);
```
 
- Représente un diplôme obtenu par un étudiant.
- `nom VARCHAR(150) NOT NULL` : le nom du diplôme (ex: "Baccalauréat").
---
 
**Lignes 151-165 — Table `bac` :**
 
```sql
CREATE TABLE bac (
    id_bac INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serie ENUM('A','C','D','L','OSE','S') NOT NULL,
    moyenne DECIMAL(4,2) DEFAULT NULL,
    mention VARCHAR(50) DEFAULT NULL,
    id_diplome INT UNSIGNED NOT NULL UNIQUE,
    ...
);
```
 
- Stocke les détails du baccalauréat d'un étudiant.
- `mention VARCHAR(50)` : calculée automatiquement côté PHP selon la moyenne.
- `id_diplome UNIQUE` : un bac correspond à exactement un diplôme.
---
 
**Tables `recommandation` et `consulter` :**
 
```sql
CREATE TABLE recommandation ( ... );
CREATE TABLE consulter ( ... );
```
 
- `recommandation` : stocke les recommandations personnalisées (score, justification) générées pour un étudiant.
- `consulter` : trace les offres de filières consultées par un étudiant (avec horodatage).
---
 
 # Fichier 2 : `config/database.php`
 
## 1. Rôle du fichier
 
Établit la **connexion à la base de données MySQL**. Sans ce fichier, PHP ne pourrait pas lire ni écrire de données.
 
## 2. Vue d'ensemble
 
Ce fichier crée une variable `$pdo` qui est un "canal de communication" avec MySQL.
 
## 3. Explication ligne par ligne
 
---
 
```php
<?php
```
 
- Balise d'ouverture PHP. Tout ce qui suit est interprété par PHP, pas affiché en HTML.
---
 
```php
$host    ="localhost";
$dbname  ="mpandova";
$user    ="root";
$password="";
```
 
- `$host` : adresse du serveur MySQL. `localhost` = sur cet ordinateur.
- `$dbname` : base de données cible.
- `$user` : utilisateur MySQL. `root` est l'administrateur par défaut en développement.
- `$password` : vide en développement local. **À sécuriser en production.**
---
 
```php
try {
    $pdo=new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8",
        $user,
        $password
    );
```
 
- `try { ... }` : bloc qui "essaie" le code. Si une erreur survient, elle est "attrapée" plus bas.
- `new PDO(...)` : crée un objet PDO (PHP Data Objects), la connexion à MySQL.
- Le DSN (Data Source Name) décrit : le moteur (`mysql`), le serveur, la base, et l'encodage.
---
 
```php
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
```
 
- `ERRMODE_EXCEPTION` : en cas d'erreur SQL, PHP lance une exception (erreur attrapable).
- `FETCH_ASSOC` : les résultats sont retournés sous forme de tableaux associatifs (`$row['nom']`).
- `EMULATE_PREPARES => false` : utilise les vraies requêtes préparées de MySQL (plus sécurisé).
---
 
```php
} catch (PDOException $e) {
    error_log("Erreur connexion BDD : " . $e->getMessage());
    die("Erreur.");
}
```
 
- `catch (PDOException $e)` : si la connexion échoue, on attrape l'erreur.
- `error_log(...)` : écrit l'erreur dans les logs serveur (invisible pour l'utilisateur).
- `die("Erreur.")` : arrête l'exécution du script avec un message sobre (ne révèle rien à un attaquant).
---
 
# Fichier 3 : `config/auth.php`
 
## 1. Rôle du fichier
 
Gère la **sécurité et les sessions**. C'est le gardien de l'application.
 
## 2. Vue d'ensemble
 
Ce fichier définit des fonctions utilisées partout pour démarrer la session, vérifier l'authentification, gérer les rôles et les messages flash.
 
## 3. Explication ligne par ligne
 
---
 
```php
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime'=>0,
        'path'    =>'/',
        'secure'  =>false,
        'httponly'=>true,
    ]);
    session_start();
}
```
 
- `session_status() === PHP_SESSION_NONE` : vérifie qu'aucune session n'est déjà démarrée.
- `lifetime => 0` : le cookie de session expire à la fermeture du navigateur.
- `httponly => true` : protège le cookie contre le vol par scripts JavaScript (protection XSS).
- `secure => false` : à mettre `true` en production avec HTTPS.
---
 
```php
function check_auth():void
{
    if (!isset($_SESSION['id_utilisateur'])) {
        header("Location:index.php");
        exit();
    }
}
```
 
- Vérifie si l'utilisateur est connecté en cherchant `$_SESSION['id_utilisateur']`.
- Si non connecté, redirige vers `index.php` et **arrête le script** (`exit()`).
- La clé de session est `id_utilisateur` (et non `id_user` comme dans l'ancienne documentation).
---
 
```php
function check_role(string $role):void
{
    check_auth();
    if ($_SESSION['role'] !== $role) {
        header("Location:index.php");
        exit();
    }
}
```
 
- Appelle d'abord `check_auth()` pour s'assurer que l'utilisateur est connecté.
- Vérifie ensuite que le rôle en session correspond au rôle attendu.
- Exemple : `check_role("etudiant")` dans `accueil_etudiant.php` bloque l'accès à un établissement.
---
 
```php
function redirect_if_logged():void
{
    if (!isset($_SESSION['id_utilisateur'])) {
        return;
    }
    
    $destinations=[
        'etudiant'     =>'accueil_etudiant.php',
        'etablissement'=>'accueil_etablissement.php',
    ];
 
    $role=$_SESSION['role'] ?? '';
    $url =$destinations[$role] ?? 'index.php';
 
    header("Location:$url");
    exit();
}
```
 
- Si un utilisateur déjà connecté tente d'accéder à `login.php` ou `register.php`, il est redirigé.
- `?? ''` : opérateur null-coalescent — si la variable n'existe pas, utiliser la valeur après `??`.
---
 
```php
function set_flash(string $type, string $message):void
{
    $_SESSION['flash']=['type'=>$type, 'message'=>$message];
}
 
function get_flash():?array
{
    if (!isset($_SESSION['flash'])) {
        return null;
    }
    $flash=$_SESSION['flash'];
    unset($_SESSION['flash']);
    return $flash;
}
```
 
- `set_flash()` : enregistre un message temporaire en session (ex: "Connexion réussie").
- `get_flash()` : récupère le message ET le supprime de la session. Le message ne s'affiche donc qu'une seule fois.
- `:?array` : retourne un tableau ou `null` (le `?` indique que null est possible).
---
 
# Fichier 4 : `config/bootstrap.php`
 
## 1. Rôle du fichier
 
**Point d'entrée de configuration** — charge tous les fichiers de configuration en une seule inclusion.
 
## 2. Explication ligne par ligne
 
```php
<?php
    require_once "../config/database.php";
    require_once "../config/auth.php";
```
 
- `require_once` : inclut le fichier une seule fois, et arrête le script s'il est introuvable.
- Après ces deux lignes, on dispose de `$pdo` (connexion DB) et de toutes les fonctions d'authentification.
---
 
# Fichier 5 : `app/views/layouts/header.php`
 
## 1. Rôle du fichier
 
**En-tête commun** à toutes les pages. Contient la structure HTML de base, la barre de navigation et l'affichage des messages flash.
 
## 2. Explication ligne par ligne
 
---
 
```html
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>mpandova</title>
        <link rel="stylesheet" href="../assets/css/output.css">
        <link rel="icon" type="image/webp" href="../assets/img/logo.webp">
    </head>
    <body class="min-h-screen flex flex-col">
```
 
- `<!DOCTYPE html>` : déclare HTML5.
- `lang="fr"` : indique la langue française (utile pour les lecteurs d'écran et le référencement).
- `meta viewport` : rend la page responsive (adaptée aux mobiles).
- `output.css` : le CSS généré par Tailwind.
- `min-h-screen flex flex-col` : le body occupe au minimum toute la hauteur de l'écran, avec un layout colonne pour que le footer reste en bas.
---
 
```html
<img src="../assets/img/bg.webp" alt="background_picture"
    class="fixed top-0 left-0 w-full h-full object-cover -z-10"
    aria-hidden="true"
/>
```
 
- Image de fond fixe en plein écran, derrière tout le contenu (`-z-10`).
- `aria-hidden="true"` : cachée aux lecteurs d'écran car purement décorative.
---
 
**Navigation selon l'état de connexion :**
 
```php
<?php if (isset($_SESSION['id_utilisateur'])): ?>
    <?php
        $dashboard = match($_SESSION['role']) {
            'etudiant'     =>'accueil_etudiant.php',
            'etablissement'=>'accueil_etablissement.php',
            default        =>'index.php',
        };
    ?>
    <a href="<?= $dashboard ?>">Mon espace</a>
    <a href="logout.php">Déconnexion</a>
<?php else: ?>
    <a href="login.php">Se connecter</a>
    <a href="register.php">S'inscrire</a>
<?php endif; ?>
```
 
- La clé de session vérifiée est `id_utilisateur` (mise à jour par rapport à l'ancienne version).
- `match()` : expression PHP 8 équivalente à un switch, mais plus concise.
---
 
**Messages flash :**
 
```php
$flash = get_flash();
if ($flash):
    $colors = [
        'success'=>'bg-green-500/90 text-white',
        'error'  =>'bg-red-500/90 text-white',
        'info'   =>'bg-blue-500/90 text-white',
    ];
    $class = $colors[$flash['type']] ?? 'bg-gray-500/90 text-white';
?>
    <div class="<?= $class ?>" role="alert">
        <?= htmlspecialchars($flash['message']) ?>
    </div>
<?php endif; ?>
```
 
- `get_flash()` récupère et supprime le message de la session.
- `htmlspecialchars()` : convertit les caractères dangereux (`<`, `>`, `"`) pour éviter les injections XSS.
- `role="alert"` : les lecteurs d'écran annoncent ce message immédiatement.
---
 
# Fichier 6 : `app/views/layouts/footer.php`
 
## 1. Rôle du fichier
 
**Pied de page commun** à toutes les pages. Ferme les balises HTML ouvertes dans `header.php`.
 
## 2. Explication ligne par ligne
 
```html
<footer class="bg-[#071d3b]/70 text-white/60 w-auto mt-auto backdrop-blur-sm">
    <div class="max-w-6xl mx-auto p-6 text-center text-sm">
        <p>© <?php date('Y') ?> Mpandova - Orientation académique à Madagascar</p>
    </div>
</footer>
</body>
</html>
```
 
- `mt-auto` : dans un conteneur flex-col, cela pousse le footer vers le bas de la page.
- `backdrop-blur-sm` : légère transparence flouée (effet "verre").
- `date('Y')` : retourne l'année courante dynamiquement.
- ⚠️ **Bug connu** : la ligne `<?php date('Y') ?>` devrait être `<?= date('Y') ?>` ou `<?php echo date('Y'); ?>` pour afficher l'année. La version actuelle n'affiche rien.
---
 
# Fichier 7 : `app/views/home.php`
 
## 1. Rôle du fichier
 
Contient le **contenu de la page d'accueil** publique (marketing et présentation).
 
## 2. Vue d'ensemble
 
Deux sections : un héros (accroche + boutons) et une grille de fonctionnalités.
 
## 3. Explication ligne par ligne
 
---
 
```html
<section class="max-w-6xl mx-auto mt-16 px-4">
    <div class="grid md:grid-cols-2 gap-10 items-center">
```
 
- `max-w-6xl mx-auto` : largeur maximale centrée.
- `grid md:grid-cols-2` : 2 colonnes sur écrans moyens (`md:` = ≥768px), 1 colonne sur mobile.
---
 
```html
<a href="register.php" class="bg-[#f1b456] text-[#071d3b] px-6 py-3 rounded-lg font-bold
    hover:bg-[#f1b456]/75 duration-500 hover:translate-y-0.5">
    Commencer
</a>
```
 
- `bg-[#f1b456]` : fond jaune/or, couleur principale de l'application.
- `hover:translate-y-0.5` : au survol, le bouton se déplace légèrement vers le bas (effet de pression).
- `duration-500` : la transition dure 500ms.
---
 
```html
<section class="max-w-6xl mx-auto mt-20 px-4">
    <div class="grid md:grid-cols-3 gap-6">
        <div class="bg-white p-6 rounded-xl shadow">
            <h3 class="font-bold mb-2">Recommandations</h3>
            <p class="text-gray-600">Suggestions adaptées à ton profil</p>
        </div>
        ...
    </div>
</section>
```
 
- Grille de 3 cartes présentant les fonctionnalités de l'application.
---
 
# Fichier 8 : `public/index.php`
 
## 1. Rôle du fichier
 
**Page d'accueil** publique.
 
## 2. Explication ligne par ligne
 
```php
<?php
    require_once "../config/bootstrap.php";
    redirect_if_logged();
    require_once "../app/views/layouts/header.php";
    require_once "../app/views/home.php";
    require_once "../app/views/layouts/footer.php";
```
 
- `redirect_if_logged()` : si l'utilisateur est déjà connecté, il est redirigé vers son espace. Inutile de voir la page d'accueil publique.
- Les trois `require_once` suivants assemblent la page : header + contenu + footer.
---
 
# Fichier 9 : `public/login.php`
 
## 1. Rôle du fichier
 
**Page de connexion** — formulaire pour entrer email et mot de passe.
 
## 2. Explication ligne par ligne
 
---
 
```html
<form action="traitement_login.php" method="POST" novalidate>
```
 
- `action` : cible de traitement du formulaire.
- `method="POST"` : les données sont envoyées dans le corps de la requête HTTP (plus sécurisé que GET pour les mots de passe).
- `novalidate` : désactive la validation HTML5 du navigateur (le projet préfère valider côté serveur en PHP).
---
 
**Pattern des floating labels (labels flottants) :**
 
```html
<input
    type="email"
    name="email"
    class="peer w-full ..."
    placeholder=" "
>
<label
    class="absolute ... peer-placeholder-shown:top-3 peer-focus:top-1 peer-focus:text-[#f1b456]"
>
    E-mail
</label>
```
 
- `peer` sur l'input : permet au label de réagir à l'état de cet input via CSS.
- `peer-placeholder-shown:...` : quand le champ est vide, le label est en position de placeholder.
- `peer-focus:...` : quand le champ est actif, le label monte en haut du champ et change de couleur.
- `placeholder=" "` : espace obligatoire pour activer le comportement du floating label.
---
 
# Fichier 10 : `public/traitement_login.php`
 
## 1. Rôle du fichier
 
**Logique de connexion** — vérifie les identifiants et crée la session.
 
## 2. Explication ligne par ligne
 
---
 
```php
$email   =filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
$password=$_POST['password'] ?? '';
```
 
- `filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)` : récupère et valide le format email. Retourne `false` si invalide.
---
 
```php
$stmt=$pdo->prepare("SELECT * FROM utilisateur WHERE email=?");
$stmt->execute([$email]);
$utilisateur=$stmt->fetch();
```
 
- La table interrogée est `utilisateur` (et non `user`).
- `prepare()` avec `?` : requête préparée — protège contre les injections SQL.
---
 
```php
if (!$utilisateur||!password_verify($password, $utilisateur['mot_de_passe_hash'])) {
    set_flash('error', 'Email ou mot de passe incorrect.');
    ...
}
```
 
- Le champ vérifié est `mot_de_passe_hash` (et non `password`).
- `password_verify()` : compare le mot de passe en clair avec son hash stocké.
---
 
```php
session_regenerate_id(true);
$_SESSION['id_utilisateur']=$utilisateur['id_utilisateur'];
$_SESSION['role']           =$utilisateur['role'];
```
 
- `session_regenerate_id(true)` : génère un nouvel ID de session (protection contre la fixation de session).
- Les clés de session sont `id_utilisateur` et `role`.
---
 
# Fichier 11 : `public/register.php`
 
## 1. Rôle du fichier
 
Page intermédiaire qui propose à l'utilisateur de **choisir son type de compte** (étudiant ou établissement) avant de s'inscrire.
 
## 2. Points clés
 
```html
<a href="register_etudiant.php">
    Étudiant — Tu cherches une filière
</a>
<a href="register_etablissement.php">
    Établissement — Tu proposes des filières
</a>
```
 
---
 
# Fichier 12 : `public/register_etudiant.php`
 
## 1. Rôle du fichier
 
**Formulaire d'inscription pour les étudiants**.
 
## 2. Points clés
 
```html
<input type="hidden" name="role" value="etudiant">
```
 
- Input caché — l'utilisateur ne le voit pas mais il est transmis avec le formulaire pour que `traitement_register.php` sache quel type de compte créer.
```html
<select id="serie_bac" name="serie_bac">
    <option value="A">Série A</option>
    <option value="C">Série C</option>
    ...
</select>
```
 
- Séries disponibles : A, C, D, L, OSE, S.
---
 
# Fichier 13 : `public/register_etablissement.php`
 
## 1. Rôle du fichier
 
**Formulaire d'inscription pour les établissements**.
 
## 2. Points clés
 
```html
<input type="hidden" name="role" value="etablissement">
 
<select id="type" name="type">
    <option value="universite_publique">Université publique</option>
    <option value="universite_privee">Université privée</option>
    <option value="grande_ecole">Grande école</option>
    <option value="institut">Institut</option>
    <option value="autre">Autre</option>
</select>
```
 
- Les types correspondent exactement aux valeurs ENUM de la base de données.
---
 
# Fichier 14 : `public/traitement_register.php`
 
## 1. Rôle du fichier
 
**Logique complète d'inscription** — valide les données et crée le compte en base.
 
## 2. Explication ligne par ligne
 
---
 
**Validation du rôle :**
 
```php
$roles_valides=['etudiant', 'etablissement'];
if (!in_array($role, $roles_valides, true)) { ... }
```
 
- `in_array($role, $roles_valides, true)` : vérifie que le rôle est dans la liste. Le `true` active la comparaison stricte.
- Sécurité : même si un utilisateur malveillant modifie l'input caché, ce test le bloque.
---
 
**Vérification de l'email :**
 
```php
$stmt=$pdo->prepare("SELECT id_utilisateur FROM utilisateur WHERE email=?");
$stmt->execute([$email]);
if ($stmt->fetch()) {
    set_flash('error', 'Cette adresse email est déjà utilisée.');
```
 
- Recherche dans la table `utilisateur` (et non `user`).
---
 
**Types valides pour l'établissement :**
 
```php
$types_valides=['universite_publique','universite_privee','grande_ecole','institut','autre'];
```
 
- Ces valeurs correspondent exactement aux valeurs ENUM de la table `etablissement`.
---
 
**Insertion en base :**
 
```php
$mot_de_passe_hash=password_hash($password, PASSWORD_DEFAULT);
 
$stmt=$pdo->prepare("INSERT INTO utilisateur(email, mot_de_passe_hash, role) VALUES(?, ?, ?)");
$stmt->execute([$email, $mot_de_passe_hash, $role]);
$id_utilisateur=$pdo->lastInsertId();
```
 
- La table est `utilisateur` avec le champ `mot_de_passe_hash`.
- `lastInsertId()` : récupère l'ID auto-incrémenté de la ligne insérée.
---
 
**Création du profil étudiant :**
 
```php
if ($role === 'etudiant') {
    $stmt=$pdo->prepare("INSERT INTO etudiant(nom, prenom, id_utilisateur) VALUES(?, ?, ?)");
    $stmt->execute([$nom, $prenom, $id_utilisateur]);
    $id_etudiant=$pdo->lastInsertId();
    
    $stmt = $pdo->prepare("INSERT INTO diplome(nom, annee_obtention, id_etudiant) VALUES (?, ?, ?)");
    $stmt->execute(['Baccalauréat', null, $id_etudiant]);
    $id_diplome = $pdo->lastInsertId();
 
    $stmt = $pdo->prepare("INSERT INTO bac(serie, moyenne, mention, id_diplome) VALUES (?, ?, ?, ?)");
    $stmt->execute([$serie, null, null, $id_diplome]);
}
```
 
- À l'inscription, un enregistrement `diplome` et `bac` sont créés avec des valeurs nulles, à compléter dans le profil.
- Chaîne d'insertions : `etudiant` → `diplome` → `bac`.
---
 
**Création de session après inscription :**
 
```php
session_regenerate_id(true);
$_SESSION['id_utilisateur']=$id_utilisateur;
$_SESSION['role']           =$role;
```
 
- L'utilisateur est automatiquement connecté après inscription.
---
 
# Fichier 15 : `public/logout.php`
 
## 1. Rôle du fichier
 
**Déconnexion sécurisée** de l'utilisateur.
 
## 2. Explication ligne par ligne
 
```php
$_SESSION = [];
```
 
- Vide toutes les données de session.
```php
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}
```
 
- `time() - 42000` : date dans le passé → le navigateur supprime automatiquement le cookie expiré.
```php
session_destroy();
header("Location: index.php");
exit();
```
 
- `session_destroy()` : détruit les données côté serveur.
---
 
# Fichier 16 : `public/accueil_etudiant.php`
 
## 1. Rôle du fichier
 
**Tableau de bord de l'étudiant** après connexion.
 
## 2. Points clés
 
```php
check_auth();
check_role("etudiant");
```
 
- Double protection : connecté ET rôle étudiant.
**Requête SQL :**
 
```php
$stmt = $pdo->prepare("
    SELECT
        e.*,
        d.nom AS diplome,
        d.annee_obtention,
        b.serie,
        b.moyenne,
        b.mention
    FROM etudiant e
    LEFT JOIN diplome d ON d.id_etudiant = e.id_etudiant
    LEFT JOIN bac b ON b.id_diplome = d.id_diplome
    WHERE e.id_utilisateur = ?
");
$stmt->execute([$_SESSION['id_utilisateur']]);
```
 
- La jointure se fait via `id_utilisateur` (et non `id_user`).
- `d.nom AS diplome` : renomme la colonne `nom` en `diplome` dans les résultats.
- La relation est : `etudiant` → `diplome` → `bac` (deux jointures).
**Affichage :**
 
```php
<h1>Bienvenue, <?= htmlspecialchars($etudiant['prenom'] . ' ' . $etudiant['nom']) ?></h1>
<p>Série bac : <?= htmlspecialchars($etudiant['serie']) ?></p>
```
 
---
 
# Fichier 17 : `public/accueil_etablissement.php`
 
## 1. Rôle du fichier
 
**Tableau de bord de l'établissement** après connexion.
 
## 2. Points clés
 
```php
check_auth();
check_role("etablissement");
 
$stmt=$pdo->prepare("SELECT * FROM etablissement WHERE id_utilisateur=?");
$stmt->execute([$_SESSION['id_utilisateur']]);
$etablissement=$stmt->fetch();
```
 
- La clé de session est `id_utilisateur`.
```html
<h1><?= htmlspecialchars($etablissement['nom']) ?></h1>
<p>Type : <?= htmlspecialchars($etablissement['type']) ?></p>
```
 
---
 
# Fichier 18 : `public/profil_etudiant.php`
 
## 1. Rôle du fichier
 
**Formulaire de modification du profil étudiant**, pré-rempli avec les données actuelles.
 
## 2. Points clés
 
**Requête SQL :**
 
```php
$stmt=$pdo->prepare("
    SELECT e.*, d.annee_obtention, b.serie, b.moyenne, b.mention
    FROM etudiant e
    LEFT JOIN diplome d ON d.id_etudiant=e.id_etudiant
    LEFT JOIN bac b ON b.id_diplome=d.id_diplome
    WHERE e.id_utilisateur=?
");
$stmt->execute([$_SESSION['id_utilisateur']]);
```
 
- La relation `diplome` → `bac` utilise `b.id_diplome=d.id_diplome` (nouvelle structure).
**Champs du formulaire :**
 
- Nom, prénom, date de naissance
- Série du bac, année d'obtention, moyenne (sur 20)
- Modification optionnelle du mot de passe
```html
<input min="0" max="20" step="0.01" type="number" name="moyenne_bac">
```
 
- `step="0.01"` : accepte deux décimales (ex: 14.75).
---
 
# Fichier 19 : `public/traitement_profil_etudiant.php`
 
## 1. Rôle du fichier
 
**Logique de mise à jour du profil étudiant**.
 
## 2. Points clés
 
**Calcul automatique de la mention :**
 
```php
function calculerMention(?float $moyenne): ?string {
    if ($moyenne === null) return null;
    if ($moyenne >= 16) return 'Très bien';
    if ($moyenne >= 14) return 'Bien';
    if ($moyenne >= 12) return 'Assez bien';
    if ($moyenne >= 10) return 'Passable';
    return null;
}
```
 
- La mention est calculée côté PHP selon la moyenne saisie.
**Conversion de la virgule :**
 
```php
$moyenne_bac=(float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
```
 
- `str_replace(',', '.')` : convertit "14,5" (notation française) en "14.5" (notation PHP/MySQL).
**Mise à jour des données :**
 
```php
$stmt=$pdo->prepare("UPDATE etudiant SET nom=?, prenom=?, date_de_naissance=? WHERE id_etudiant=?");
$stmt->execute([$nom, $prenom, !empty($ddn) ? $ddn : null, $etudiant['id_etudiant']]);
```
 
```php
$stmt=$pdo->prepare("UPDATE diplome SET annee_obtention=? WHERE id_diplome=?");
$stmt->execute([$annee>0 ? $annee : null, $diplome['id_diplome']]);
 
$stmt=$pdo->prepare("UPDATE bac SET serie=?, moyenne=?, mention=? WHERE id_bac=?");
$stmt->execute([$serie, $moyenne_bac, $mention, $diplome['id_bac']]);
```
 
**Mise à jour du mot de passe :**
 
```php
if (!empty($password)) {
    $hash=password_hash($password, PASSWORD_DEFAULT);
    $stmt=$pdo->prepare("UPDATE utilisateur SET mot_de_passe_hash=? WHERE id_utilisateur=?");
    $stmt->execute([$hash, $_SESSION['id_utilisateur']]);
}
```
 
- La table est `utilisateur` et le champ `mot_de_passe_hash`.
> ⚠️ **Bug dans le code actuel** : le fichier utilise encore `UPDATE user SET password=? WHERE id_user=?` — à corriger en `UPDATE utilisateur SET mot_de_passe_hash=? WHERE id_utilisateur=?`.
 
---
 
# Fichier 20 : `public/profil_etablissement.php`
 
## 1. Rôle du fichier
 
**Formulaire de modification du profil établissement**, pré-rempli avec les données actuelles.
 
## 2. Points clés
 
**Requête SQL :**
 
```php
$stmt=$pdo->prepare("
    SELECT e.*, l.ville, l.adresse
    FROM etablissement e
    LEFT JOIN location l ON l.id_etablissement=e.id_etablissement
    WHERE e.id_user=?
");
```
 
> ⚠️ **Bug dans le code actuel** : la table de localisation s'appelle `localisation` dans le SQL, mais le fichier utilise `location` et `id_user` au lieu de `id_utilisateur`. À corriger.
 
**Champs du formulaire :**
 
- Nom, type, site web
- Ville, adresse (localisation)
- Modification optionnelle du mot de passe
---
 
# Fichier 21 : `public/traitement_profil_etablissement.php`
 
## 1. Rôle du fichier
 
**Logique de mise à jour du profil établissement**.
 
## 2. Points clés
 
**Validation :**
 
```php
$types_valides=['universite', 'grande_ecole', 'institut_prive', 'lycee_technique', 'autre'];
```
 
> ⚠️ **Incohérence** : les types valides ici (`universite`, `lycee_technique`, etc.) ne correspondent pas aux valeurs ENUM de la base de données (`universite_publique`, `universite_privee`, `grande_ecole`, `institut`, `autre`). À corriger.
 
**Logique upsert pour la localisation :**
 
```php
$stmt=$pdo->prepare("SELECT id_etablissement FROM location WHERE id_etablissement=?");
$stmt->execute([$etablissement['id_etablissement']]);
$loc=$stmt->fetch();
 
if ($loc) {
    $stmt=$pdo->prepare("UPDATE location SET ville=?, adresse=? WHERE id_etablissement=?");
} else {
    $stmt=$pdo->prepare("INSERT INTO location(id_etablissement, ville, adresse) VALUES(?, ?, ?)");
}
```
 
- Si la localisation existe, on la met à jour ; sinon on l'insère.
- ⚠️ La table devrait être `localisation` (et non `location`).
---
 
# Fichier 22 : `assets/css/input.css`
 
## 1. Rôle du fichier
 
**Point d'entrée de Tailwind CSS**.
 
```css
@import "tailwindcss";
```
 
- Tailwind v4 utilise une seule directive d'import pour charger toutes les classes utilitaires.
- Le CLI Tailwind lit ce fichier et génère `output.css` avec toutes les classes effectivement utilisées dans le projet.
---
 
# Fichier 23 : `package.json`
 
## 1. Rôle du fichier
 
**Configuration Node.js** — utilisé uniquement pour Tailwind CSS.
 
```json
{
  "dependencies": {
    "@tailwindcss/cli": "^4.3.0"
  },
  "devDependencies": {
    "tailwindcss": "^4.3.0"
  }
}
```
 
- `^4.3.0` : version 4.3.0 ou plus récente compatible (pas 5.x).
- `@tailwindcss/cli` : l'outil en ligne de commande pour compiler le CSS.
---
 
# Flux global de l'application
 
## Scénario 1 : Un étudiant s'inscrit
 
```
1. /public/index.php                → Page d'accueil, bouton "Commencer"
2. /public/register.php             → Choisit "Étudiant"
3. /public/register_etudiant.php    → Remplit le formulaire
4. /public/traitement_register.php  → Validation + insertion DB
   - INSERT INTO utilisateur
   - INSERT INTO etudiant
   - INSERT INTO diplome (baccalauréat vide)
   - INSERT INTO bac (valeurs nulles)
   - Création de session
5. /public/accueil_etudiant.php     → Tableau de bord
```
 
## Scénario 2 : Un utilisateur se connecte
 
```
1. /public/login.php              → Formulaire de connexion
2. /public/traitement_login.php   → SELECT FROM utilisateur WHERE email=?
                                  → password_verify($password, $mot_de_passe_hash)
                                  → $_SESSION['id_utilisateur'] + ['role']
3. → Redirection vers accueil_etudiant.php ou accueil_etablissement.php
```
 
## Scénario 3 : Protection des pages
 
```
accueil_etudiant.php appelle check_auth() + check_role("etudiant")
→ check_auth() : isset($_SESSION['id_utilisateur']) ?
  - Non → redirect vers index.php
  - Oui → check_role()
→ check_role() : $_SESSION['role'] === 'etudiant' ?
  - Non → redirect vers index.php
  - Oui → affichage de la page
```
 
## Scénario 4 : Mise à jour du profil étudiant
 
```
1. /public/profil_etudiant.php              → Affiche le formulaire pré-rempli
2. /public/traitement_profil_etudiant.php   → Validation des données
   - UPDATE etudiant SET nom, prenom, date_de_naissance
   - UPDATE diplome SET annee_obtention
   - UPDATE bac SET serie, moyenne, mention (calculée automatiquement)
   - UPDATE utilisateur SET mot_de_passe_hash (si changement)
3. → Flash "Profil mis à jour" + redirect vers profil_etudiant.php
```
 
---
 
# Points d'attention et bugs connus
 
| Fichier | Problème | Correction nécessaire |
|---|---|---|
| `footer.php` | `<?php date('Y') ?>` n'affiche rien | Utiliser `<?= date('Y') ?>` |
| `traitement_profil_etudiant.php` | `UPDATE user SET password=?` | → `UPDATE utilisateur SET mot_de_passe_hash=?` |
| `traitement_profil_etudiant.php` | `WHERE id_user=?` avec `$_SESSION['id_user']` | → `WHERE id_utilisateur=?` avec `$_SESSION['id_utilisateur']` |
| `traitement_profil_etablissement.php` | `UPDATE user SET password=?` idem | → `UPDATE utilisateur SET mot_de_passe_hash=?` |
| `traitement_profil_etablissement.php` | Types valides incohérents avec la BDD | Aligner avec les ENUM de `mpandova.sql` |
| `profil_etablissement.php` | Jointure sur `location` et `id_user` | → `localisation` et `id_utilisateur` |
 
---
 
# Concepts clés récapitulatifs
 
| Concept | Explication simple |
|---|---|
| **Session PHP** | Mémoire temporaire côté serveur liée à un navigateur via un cookie |
| **Requête préparée** | Requête SQL avec des `?` pour éviter les injections SQL |
| **Password hashing** | Transformation du mot de passe en code illisible stocké en DB |
| **Flash message** | Message stocké en session, affiché une seule fois puis supprimé |
| **Clé étrangère** | Champ qui référence la clé primaire d'une autre table |
| **PDO** | Couche d'abstraction PHP pour parler à différents types de DB |
| **Tailwind CSS** | Framework CSS basé sur des classes utilitaires |
| **LEFT JOIN** | Jointure SQL qui garde toutes les lignes de la table gauche |
| **AUTO_INCREMENT** | Compteur automatique pour les identifiants uniques |
| **ENUM** | Type SQL qui restreint les valeurs possibles à une liste |
| **CASCADE** | Suppression/mise à jour automatique des enregistrements liés |
| **DECIMAL(4,2)** | Nombre décimal avec 4 chiffres au total et 2 après la virgule |
| **Upsert** | Opération qui insère ou met à jour selon l'existence d'un enregistrement |
| **Floating label** | Label CSS qui monte dans le champ quand on clique dessus |
| **Peer (Tailwind)** | Classe permettant à un élément de styler son voisin selon son état |