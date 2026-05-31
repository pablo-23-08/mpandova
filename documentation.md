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

Ce fichier crée une variable `$pdo` qui est un "canal de communication" avec MySQL. Tous les autres fichiers PHP utilisent cette variable pour faire des requêtes.

## 3. Explication ligne par ligne

---

**Ligne 1 :**

```php
<?php
```

- Balise d'ouverture PHP. Tout ce qui suit est du code PHP, pas du HTML. PHP est un langage interprété côté serveur.

---

**Lignes 2-5 :**

```php
    $host    ="localhost";
    $dbname  ="mpandova";
    $user    ="root";
    $password="";
```

- `$host` : adresse du serveur MySQL. `localhost` = "sur cet ordinateur même".
- `$dbname` : nom de la base de données à utiliser (créée dans le fichier SQL).
- `$user` : nom d'utilisateur MySQL. `root` est l'administrateur par défaut en développement local.
- `$password` : mot de passe MySQL. Vide ici car c'est un environnement de développement local.
- ** En production (sur un vrai serveur en ligne), il faudrait mettre un vrai mot de passe et ne jamais l'exposer publiquement.**

---

**Ligne 7 :**

```php
    try {
```

- Début d'un bloc `try`. PHP va "essayer" d'exécuter le code à l'intérieur. Si une erreur se produit, elle sera "attrapée" par le bloc `catch`.
- C'est comme dire : "Essaie de faire ça, et si ça échoue, fais autre chose."

---

**Lignes 8-12 :**

```php
        $pdo=new PDO(
            "mysql:host=$host;dbname=$dbname;charset=utf8",
            $user,
            $password
        );
```

- `new PDO(...)` : crée un nouvel objet PDO (PHP Data Objects). PDO est une classe PHP qui fournit une interface uniforme pour se connecter à différentes bases de données.
- `"mysql:host=$host;dbname=$dbname;charset=utf8"` : le DSN (Data Source Name), une chaîne qui décrit comment se connecter :
  - `mysql:` : utiliser le driver MySQL
  - `host=$host` : serveur = valeur de $host (localhost)
  - `dbname=$dbname` : base = mpandova
  - `charset=utf8` : encodage UTF-8 pour les données
- `$user`, `$password` : identifiants de connexion.
- Le résultat est stocké dans `$pdo` — c'est notre "ligne téléphonique" vers MySQL.

---

**Lignes 13-15 :**

```php
        $pdo->setAttribute(PDO::ATTR_ERRMODE,PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES,false);
```

- `setAttribute` : configure le comportement de PDO.
- `PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION` : en cas d'erreur SQL, lancer une exception (erreur attrapable) plutôt que de passer silencieusement.
- `PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC` : quand on récupère des données, les retourner sous forme de tableau associatif (clés = noms des colonnes). Ex: `$user['email']` plutôt que `$user[0]`.
- `PDO::ATTR_EMULATE_PREPARES, false` : désactiver l'émulation des requêtes préparées — PDO utilisera les vraies requêtes préparées de MySQL, ce qui est plus sécurisé.

---

**Lignes 16-19 :**

```php
    } catch (PDOException $e) {
        error_log("Erreur connexion BDD : " . $e->getMessage());
        die("Erreur.");
    }
```

- `catch (PDOException $e)` : si la connexion échoue, attraper l'erreur de type `PDOException` et la stocker dans `$e`.
- `error_log(...)` : écrire le message d'erreur dans les logs du serveur (pas visible pour l'utilisateur, mais lisible par le développeur).
- `$e->getMessage()` : récupère le message d'erreur détaillé.
- `die("Erreur.")` : arrêter l'exécution du script et afficher "Erreur." — très sobre pour ne pas révéler des informations sensibles à un éventuel attaquant.

---

# Fichier 3 : `config/auth.php`

## 1. Rôle du fichier

Gère la **sécurité et les sessions utilisateurs**. C'est le "gardien" de l'application.

## 2. Vue d'ensemble

Ce fichier définit des **fonctions** qui sont utilisées partout dans l'application pour :

- Démarrer une session sécurisée
- Vérifier si un utilisateur est connecté
- Vérifier le rôle de l'utilisateur
- Gérer les messages flash (notifications temporaires)

## 3. Explication ligne par ligne

---

**Lignes 2-11 :**

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

- `session_status() === PHP_SESSION_NONE` : vérifie si une session n'est pas encore démarrée. Sans ce test, appeler `session_start()` deux fois causerait une erreur.
- `session_set_cookie_params(...)` : configure le cookie de session AVANT de le démarrer :
  - `lifetime => 0` : le cookie expire quand le navigateur est fermé (pas de "rester connecté")
  - `path => '/'` : le cookie est valide pour tout le site
  - `secure => false` : le cookie peut être envoyé en HTTP (mettre `true` en production avec HTTPS)
  - `httponly => true` : le cookie n'est pas accessible via JavaScript (protection contre le vol de session par des scripts malveillants — attaque XSS)
- `session_start()` : démarre la session. PHP crée un identifiant unique de session et envoie un cookie au navigateur.

---

**Lignes 15-21 :**

```php
        function check_auth():void
        {
            if (!isset($_SESSION['id_user'])){
                header("Location:index.php");
                exit();
            }
        }
```

- `function check_auth():void` : déclare une fonction. `:void` signifie qu'elle ne retourne aucune valeur.
- `!isset($_SESSION['id_user'])` : `isset()` vérifie si une variable existe et n'est pas null. `!` = "NOT". Donc : "si `$_SESSION['id_user']` n'existe pas..."
- `$_SESSION` : super-variable PHP qui stocke des données de session côté serveur. Elle persiste entre les pages pour un même utilisateur.
- `header("Location:index.php")` : envoie un en-tête HTTP de redirection — le navigateur va aller à `index.php`.
- `exit()` : TRÈS IMPORTANT. Sans `exit()`, le code PHP continuerait de s'exécuter même après la redirection, ce qui serait un problème de sécurité.

---

**Lignes 24-31 :**

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

- `check_role(string $role)` : prend un paramètre `$role` de type `string`.
- `check_auth()` : appelle d'abord `check_auth()` — si l'utilisateur n'est pas connecté, il est redirigé avant même de vérifier le rôle.
- `$_SESSION['role'] !== $role` : si le rôle en session ne correspond pas au rôle attendu, rediriger.
- Utilisation : `check_role("etudiant")` dans `accueil_etudiant.php` garantit qu'un établissement ne peut pas accéder à l'espace étudiant.

---

**Lignes 34-47 :**

```php
        function redirect_if_logged():void
        {
            if (!isset($_SESSION['id_user'])) {
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

- But : si un utilisateur déjà connecté essaie d'accéder à `login.php` ou `register.php`, le rediriger vers son espace.
- `return` : quitte la fonction sans rien faire si l'utilisateur n'est pas connecté.
- `$destinations = [...]` : tableau associatif (dictionnaire) qui mappe un rôle vers une URL.
- `$_SESSION['role'] ?? ''` : opérateur null-coalescent `??`. Si `$_SESSION['role']` existe, l'utiliser, sinon utiliser `''` (chaîne vide).
- `$destinations[$role] ?? 'index.php'` : cherche l'URL pour le rôle, ou retourne `index.php` si non trouvé.

---

**Lignes 51-66 :**

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

- Les **messages flash** sont des messages qui s'affichent une seule fois (après une action comme un login réussi ou échoué).
- `set_flash(string $type, string $message)` : enregistre un message dans la session. Ex: `set_flash('error', 'Email ou mot de passe incorrect.')`.
- `get_flash():?array` : le `?array` signifie "retourne un tableau ou null".
  - Si pas de flash, retourne `null`.
  - Sinon, stocke le flash, **le supprime de la session** (`unset`), et le retourne. C'est pourquoi le message ne s'affiche qu'une seule fois.

---

# Fichier 4 : `config/bootstrap.php`

## 1. Rôle du fichier

C'est le **point d'entrée de configuration** — il charge tous les fichiers de configuration nécessaires en une seule ligne.

## 2. Vue d'ensemble

Au lieu d'inclure `database.php` et `auth.php` séparément dans chaque fichier PHP, on inclut juste `bootstrap.php`.

## 3. Explication ligne par ligne

---

**Ligne 1 :**

```php
<?php
```

Ouverture PHP.

---

**Ligne 2 :**

```php
    require_once "../config/database.php";
```

- `require_once` : inclut le fichier `database.php`. La différence avec `include` :
  - `require` vs `include` : `require` arrête le script si le fichier n'existe pas ; `include` affiche juste un avertissement.
  - `_once` : même si `require_once` est appelé plusieurs fois, le fichier ne sera inclus qu'une seule fois. Évite les redéclarations d'erreurs.
- `"../config/database.php"` : chemin relatif. `..` signifie "remonter d'un dossier". Depuis `config/`, `../config/` = `config/`.

---

**Ligne 3 :**

```php
    require_once "../config/auth.php";
```

- Même logique : inclut `auth.php` pour avoir accès aux fonctions de session et d'authentification.
- Après ces deux lignes, on a accès à `$pdo` (connexion DB) et à toutes les fonctions auth.

---

# Fichier 5 : `app/views/layouts/header.php`

## 1. Rôle du fichier

L'**en-tête commun** à toutes les pages. Il contient la structure HTML de base, la barre de navigation, et le système d'affichage des messages flash.

## 2. Vue d'ensemble

Ce fichier est inclus en haut de chaque page. Il génère : `<!DOCTYPE html>`, le `<head>`, et la `<header>` avec la navigation.

## 3. Explication ligne par ligne

---

**Lignes 1-4 :**

```html
<!DOCTYPE html>
<html lang="fr">
    <head>
        <meta charset="UTF-8">
```

- `<!DOCTYPE html>` : déclare que c'est une page HTML5 (la version moderne de HTML).
- `<html lang="fr">` : élément racine du document. `lang="fr"` indique que la langue est le français (important pour les lecteurs d'écran et le référencement).
- `<head>` : section invisible de métadonnées (informations sur la page, pas son contenu).
- `<meta charset="UTF-8">` : dit au navigateur d'utiliser l'encodage UTF-8 pour afficher correctement les accents.

---

**Ligne 5 :**

```html
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
```

- Indispensable pour les sites "responsive" (qui s'adaptent aux mobiles).
- `width=device-width` : la largeur de la page = la largeur de l'écran.
- `initial-scale=1.0` : pas de zoom initial.
- Sans cette ligne, les mobiles afficheraient la page comme sur un grand écran, tout petit.

---

**Ligne 6 :**

```html
        <title>mpandova</title>
```

- Titre affiché dans l'onglet du navigateur.

---

**Ligne 7 :**

```html
        <link rel="stylesheet" href="../assets/css/output.css">
```

- Charge la feuille de style CSS générée par Tailwind. Sans ce fichier, la page serait sans style.

---

**Ligne 8 :**

```html
        <link rel="icon" type="image/webp" href="../assets/img/logo.webp">
```

- Définit le favicon (petite icône dans l'onglet du navigateur).

---

**Ligne 11 :**

```html
        <body class="min-h-screen flex flex-col">
```

- Classes Tailwind CSS :
  - `min-h-screen` : hauteur minimale = 100% de l'écran
  - `flex` : utilise Flexbox (modèle de mise en page)
  - `flex-col` : les éléments enfants s'empilent verticalement

---

**Lignes 13-17 :**

```html
        <img src="../assets/img/bg.webp" alt="background_picture"
            class="fixed top-0 left-0 w-full h-full object-cover -z-10"
            aria-hidden="true"
        />
        <div class="fixed inset-0 bg-[url('../assets/img/bg.webp')] bg-cover bg-center -z-20"></div>
```

- L'image de fond (`bg.webp`) est affichée en position fixe derrière tout le contenu.
- `fixed` : reste en place même si on défile.
- `object-cover` : l'image couvre tout l'espace sans déformation.
- `-z-10` : z-index négatif, derrière tous les autres éléments.
- `aria-hidden="true"` : cache l'image aux lecteurs d'écran (c'est décoratif).

---

**Lignes 27-46 (navigation) :**

```php
                <?php if (isset($_SESSION['id_user'])): ?>
```

- Début d'une condition PHP dans du HTML. Si l'utilisateur est connecté (`$_SESSION['id_user']` existe), afficher un menu différent.

```php
                        $dashboard = match($_SESSION['role']) {
                            'etudiant'     =>'accueil_etudiant.php',
                            'etablissement'=>'accueil_etablissement.php',
                            default        =>'index.php',
                        };
```

- `match` : expression PHP 8 similaire à `switch` mais plus concise. Selon le rôle, détermine l'URL du dashboard.
- `default` : si aucun rôle ne correspond (ne devrait pas arriver), aller à l'index.

---

**Lignes 60-79 (messages flash) :**

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
            <div class="<?= $class ?> text-center px-4 py-3 text-sm font-medium" role="alert">
                <?= htmlspecialchars($flash['message']) ?>
            </div>
        <?php endif; ?>
```

- `get_flash()` : récupère et supprime le message flash de la session.
- `$colors` : mappe le type de message à des classes CSS Tailwind (vert pour succès, rouge pour erreur).
- `<?= $class ?>` : syntaxe courte PHP pour `<?php echo $class; ?>`.
- `htmlspecialchars($flash['message'])` : **sécurité importante** — convertit les caractères HTML spéciaux (`<`, `>`, `"`) pour éviter les injections XSS.
- `role="alert"` : attribut d'accessibilité — les lecteurs d'écran annoncent ce message immédiatement.

---

# Fichier 6 : `app/views/layouts/footer.php`

## 1. Rôle du fichier

Le **pied de page commun** à toutes les pages.

## 2. Explication ligne par ligne

---

**Ligne 1-4 :**

```html
    <footer class="bg-[#071d3b]/70 text-white/60 w-auto mt-auto backdrop-blur-sm">
        <div class="max-w-6xl mx-auto p-6 text-center text-sm">
            <p>© <php? date('Y') ?> Mpandova - Orientation académique à Madagascar</p>
        </div>
    </footer>
```

- `bg-[#071d3b]/70` : couleur de fond personnalisée avec 70% d'opacité.
- `mt-auto` : `margin-top: auto`. Dans un conteneur flex-col, cela pousse le footer vers le bas de la page.
- `backdrop-blur-sm` : légère transparence flouée (effet "verre").
- `date('Y')` : fonction PHP qui retourne l'année courante (ex: 2025). ⚠️ Note : il y a un bug dans le fichier — `<php?` devrait être `<?php`.

---

**Lignes 6-7 :**

```html
</body>
</html>
```

- Ferme les balises `<body>` et `<html>` ouvertes dans `header.php`. Les fichiers de layout fonctionnent en paire : header ouvre, footer ferme.

---

# Fichier 7 : `app/views/home.php`

## 1. Rôle du fichier

Contient le **contenu de la page d'accueil** (marketing et présentation).

## 2. Vue d'ensemble

Deux sections : un héros (texte + image) et une présentation des fonctionnalités.

## 3. Explication ligne par ligne

---

**Lignes 1-19 (section héros) :**

```html
<section class="max-w-6xl mx-auto mt-16 px-4">
    <div class="grid md:grid-cols-2 gap-10 items-center">
```

- `max-w-6xl mx-auto` : largeur maximale de 72rem (1152px) et centré.
- `grid md:grid-cols-2` : grille en 2 colonnes sur les écrans moyens et grands (`md:` = media query ≥768px). Sur mobile, une seule colonne.
- `gap-10` : espace entre les colonnes.

```html
            <h1 class="text-4xl font-bold text-[#071d3b] text-shadow-lg text-shadow-blue-300 mb-6">
                Trouve ta voie académique facilement
            </h1>
```

- `text-4xl` : taille de police 2.25rem (36px).
- `text-[#071d3b]` : couleur personnalisée en hexadécimal — bleu marine foncé.

```html
            <a href="register.php" class="shadow shadow-blue-300 bg-[#f1b456] text-[#071d3b] px-6 py-3 rounded-lg font-bold hover:bg-[#f1b456]/75 duration-500 hover:translate-y-0.5">
                Commencer
            </a>
```

- `bg-[#f1b456]` : fond jaune/or.
- `hover:bg-[#f1b456]/75` : au survol, la couleur devient 75% opaque.
- `duration-500` : la transition dure 500ms.
- `hover:translate-y-0.5` : au survol, le bouton se déplace légèrement vers le bas (2px) — effet de pression.

---

# Fichier 8 : `public/index.php`

## 1. Rôle du fichier

La **page d'accueil** publique du site.

## 2. Explication ligne par ligne

---

```php
<?php
    require_once "../config/bootstrap.php";
    redirect_if_logged();
    require_once "../app/views/layouts/header.php";
    require_once "../app/views/home.php";
    require_once "../app/views/layouts/footer.php";
```

- `require_once "../config/bootstrap.php"` : charge la connexion DB et les fonctions auth.
- `redirect_if_logged()` : si l'utilisateur est déjà connecté, le rediriger vers son espace. Un utilisateur connecté n'a pas besoin de voir la page d'accueil publique.
- Les trois `require_once` suivants assemblent la page : header + contenu + footer.

---

# Fichier 9 : `public/login.php`

## 1. Rôle du fichier

La **page de connexion** — formulaire pour entrer email et mot de passe.

## 2. Explication ligne par ligne

---

```php
<?php
    require_once "../config/bootstrap.php";
    redirect_if_logged();
    include '../app/views/layouts/header.php';
?>
```

- `include` vs `require_once` : ici `include` est utilisé. La différence est subtile : `include` ne stoppe pas le script si le fichier est manquant.

---

```html
<form action="traitement_login.php" method="POST" novalidate>
```

- `action="traitement_login.php"` : quand le formulaire est soumis, les données sont envoyées à `traitement_login.php`.
- `method="POST"` : les données sont envoyées dans le corps de la requête HTTP (pas dans l'URL). C'est plus sécurisé pour des mots de passe qu'avec `GET`.
- `novalidate` : désactive la validation HTML5 native du navigateur (le projet préfère valider côté serveur en PHP).

---

```html
<input
    type="email"
    id="email"
    name="email"
    required
    class="peer w-full border border-black/20 text-white rounded-lg px-4 pt-5 
    pb-2 focus:outline-none focus:border-[#f1b456] focus:ring-1 focus:ring-[#f1b456]"
    placeholder=" "
>
```

- `type="email"` : le navigateur sait que c'est un email (affiche le bon clavier sur mobile).
- `id="email"` : identifiant unique pour la liaison avec le `<label>`.
- `name="email"` : nom du champ dans les données POST — accessible via `$_POST['email']` en PHP.
- `placeholder=" "` : espace invisible nécessaire pour le trick CSS du label flottant (voir ci-dessous).
- `peer` : classe Tailwind permettant à un élément sibling (le label) de réagir à l'état de cet input.

---

```html
<label
    for="email"
    class="absolute left-4 top-3 text-white/90 text-sm transition-all 
    peer-placeholder-shown:top-3 peer-placeholder-shown:text-base peer-placeholder-shown:text-white/70
    peer-focus:top-1 peer-focus:text-sm peer-focus:text-[#f1b456] -mt-1"
>
    E-mail
</label>
```

- `for="email"` : lie le label à l'input avec `id="email"`. Cliquer sur le label focus l'input.
- `peer-placeholder-shown:...` : quand le champ est vide (placeholder visible), le label est en position normale (comme un placeholder).
- `peer-focus:...` : quand le champ est en focus, le label monte et change de couleur.
- C'est le pattern des "floating labels" — labels qui montent quand on clique sur le champ.

---

# Fichier 10 : `public/traitement_login.php`

## 1. Rôle du fichier

Le **cerveau de la connexion** — vérifie les identifiants et crée la session.

## 2. Vue d'ensemble

Ce fichier ne s'affiche jamais directement. Il reçoit les données du formulaire de login, les valide, vérifie en base de données, et redirige.

## 3. Explication ligne par ligne

---

**Lignes 4-5 :**

```php
    $email   =filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL);
    $password=$_POST['password'] ?? '';
```

- `filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL)` : récupère `$_POST['email']` ET valide que c'est un email valide. Retourne `false` ou `null` si invalide.
- `$_POST['password'] ?? ''` : récupère le mot de passe ou `''` si absent.

---

**Lignes 7-11 :**

```php
    if (!$email||empty($password)) {
        set_flash('error', 'Veuillez remplir tous les champs correctement.');
        header("Location: login.php");
        exit();
    }
```

- Validation basique : si email invalide ou mot de passe vide, afficher une erreur et retourner à login.

---

**Lignes 14-16 :**

```php
    $stmt=$pdo->prepare("SELECT * FROM user WHERE email=?");
    $stmt->execute([$email]);
    $user=$stmt->fetch();
```

- `$pdo->prepare(...)` : crée une **requête préparée**. Le `?` est un paramètre — jamais insérer des variables directement dans SQL (risque d'injection SQL).
- `$stmt->execute([$email])` : exécute la requête en remplaçant `?` par `$email` de façon sécurisée.
- `$stmt->fetch()` : récupère la première ligne de résultat sous forme de tableau associatif.

---

**Lignes 18-22 :**

```php
    if (!$user||!password_verify($password, $user['password'])) {
        set_flash('error', 'Email ou mot de passe incorrect.');
        header("Location: login.php");
        exit();
    }
```

- `!$user` : aucun utilisateur trouvé avec cet email.
- `!password_verify($password, $user['password'])` : `password_verify` compare un mot de passe en clair avec un hash. **On ne stocke jamais les mots de passe en clair** — ils sont hachés avec `password_hash()` à l'inscription.
- Le message d'erreur est intentionnellement vague ("Email ou mot de passe incorrect") pour ne pas révéler si c'est l'email ou le mot de passe qui est faux.

---

**Lignes 27-29 :**

```php
    session_regenerate_id(true);
    $_SESSION['id_user']=$user['id_user'];
    $_SESSION['role']   =$user['role'];
```

- `session_regenerate_id(true)` : génère un nouvel identifiant de session. Prévient l'attaque de **fixation de session** (un attaquant qui aurait obtenu un ID de session avant la connexion ne pourra plus l'utiliser).
- Stocke l'ID utilisateur et le rôle en session.

---

# Fichier 11 : `public/register.php`

## 1. Rôle du fichier

Page intermédiaire qui demande à l'utilisateur de **choisir son type de compte** (étudiant ou établissement).

## 2. Vue d'ensemble

Page simple avec deux cartes cliquables qui redirigent vers le formulaire approprié.

---

# Fichier 12 : `public/register_etudiant.php`

## 1. Rôle du fichier

**Formulaire d'inscription pour les étudiants**.

## 2. Points clés

```html
<input type="hidden" name="role" value="etudiant">
```

- Input caché — l'utilisateur ne le voit pas, mais il est envoyé avec le formulaire pour que `traitement_register.php` sache quel type de compte créer.

```html
<select id="serie_bac" name="serie_bac" required ...>
    <option value="A" class="bg-[#071d3b]/50">Série A</option>
    ...
```

- Menu déroulant pour la série du bac. `class="bg-[#071d3b]/50"` sur les options donne un fond coloré dans certains navigateurs.

---

# Fichier 13 : `public/traitement_register.php`

## 1. Rôle du fichier

**Logique complète d'inscription** — valide toutes les données et crée le compte.

## 2. Vue d'ensemble

Ce fichier gère les deux types d'inscription (étudiant ET établissement) dans un seul fichier, grâce à la variable `$role`.

## 3. Points clés

---

**Validation du rôle :**

```php
    $roles_valides=['etudiant', 'etablissement'];

    if (!in_array($role, $roles_valides, true)) {
```

- `in_array($role, $roles_valides, true)` : vérifie que `$role` est dans le tableau. Le `true` active la comparaison stricte (type ET valeur).
- **Sécurité** : même si l'input caché `role` est modifié par un utilisateur malveillant, ce test le bloque.

---

**Vérification de l'email existant :**

```php
    $stmt=$pdo->prepare("SELECT id_user FROM user WHERE email=?");
    $stmt->execute([$email]);

    if ($stmt->fetch()) {
        set_flash('error', 'Cette adresse email est déjà utilisée.');
```

- Si `fetch()` retourne quelque chose, c'est que l'email existe déjà.

---

**Insertion en base de données :**

```php
        $password_hash=password_hash($password, PASSWORD_DEFAULT);

        $stmt=$pdo->prepare("INSERT INTO user(email, password, role) VALUES(?, ?, ?)");
        $stmt->execute([$email, $password_hash, $role]);
        $id_user=$pdo->lastInsertId();
```

- `password_hash($password, PASSWORD_DEFAULT)` : crée un hash sécurisé du mot de passe. `PASSWORD_DEFAULT` utilise l'algorithme bcrypt (recommandé).
- `$pdo->lastInsertId()` : récupère l'ID auto-incrémenté de la ligne qu'on vient d'insérer. Nécessaire pour créer ensuite le profil étudiant ou établissement lié.

---

# Fichier 14 : `public/logout.php`

## 1. Rôle du fichier

**Déconnexion sécurisée** de l'utilisateur.

## 2. Explication ligne par ligne

---

```php
    $_SESSION = [];
```

- Vide complètement le tableau `$_SESSION`. Supprime toutes les données de session.

---

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

- `ini_get("session.use_cookies")` : vérifie si les sessions utilisent des cookies.
- `session_get_cookie_params()` : récupère les paramètres du cookie de session actuel.
- `setcookie(..., time() - 42000, ...)` : recréé le cookie avec une date d'expiration dans le passé (42000 secondes = ~11.6 heures). Le navigateur supprime automatiquement les cookies expirés.
- C'est la façon standard de supprimer un cookie : on ne peut pas vraiment "supprimer" un cookie, on le fait expirer.

---

```php
    session_destroy();
    header("Location: index.php");
    exit();
```

- `session_destroy()` : détruit les données de session côté serveur.
- Redirection vers l'accueil.

---

# Fichier 15 : `public/accueil_etudiant.php`

## 1. Rôle du fichier

**Tableau de bord de l'étudiant** après connexion.

## 2. Points clés

```php
    check_auth();
    check_role("etudiant");
```

- Double protection : vérifie que l'utilisateur est connecté ET qu'il est étudiant.

```php
    $stmt = $pdo->prepare("SELECT e.*, b.serie, b.moyenne, b.annee
        FROM etudiant e
        LEFT JOIN diplome d ON d.id_etudiant = e.id_etudiant
        LEFT JOIN bac b ON b.id_bac = d.id_bac
        WHERE e.id_user = ?"
    );
```

- `LEFT JOIN` : jointure gauche — retourne toutes les lignes de `etudiant` même si `diplome` et `bac` sont vides.
- `e.*` : toutes les colonnes de la table `etudiant`.
- `b.serie, b.moyenne, b.annee` : colonnes spécifiques de la table `bac`.

---

# Fichier 16 : `public/profil_etudiant.php` et `public/traitement_profil_etudiant.php`

## 1. Rôle

`profil_etudiant.php` affiche le formulaire de modification du profil pré-rempli avec les données actuelles.
`traitement_profil_etudiant.php` traite les modifications soumises.

## 2. Points clés dans `traitement_profil_etudiant.php`

```php
$moyenne=(float) str_replace(',', '.', $_POST['moyenne_bac'] ?? 0);
```

- `str_replace(',', '.')` : remplace la virgule par un point. En France, on écrit "14,5" mais PHP/MySQL attendent "14.5".
- `(float)` : cast (conversion) en nombre flottant.

---

```php
        if ($diplome) {
            $stmt=$pdo->prepare("UPDATE bac SET serie=?, annee=?, moyenne=? WHERE id_bac=?");
            $stmt->execute([$serie, $annee, $moyenne > 0 ? $moyenne : null, $diplome['id_bac']]);
        } else {
            $stmt=$pdo->prepare("INSERT INTO bac(serie, annee, moyenne) VALUES(?, ?, ?)");
```

- Logique d'upsert (update ou insert) : si un diplôme existe déjà, le mettre à jour ; sinon le créer.
- `$moyenne > 0 ? $moyenne : null` : opérateur ternaire — si moyenne > 0, utiliser la moyenne, sinon stocker NULL.

---

# Fichier 17 : `assets/css/input.css`

## 1. Rôle du fichier

**Point d'entrée de Tailwind CSS**.

---

```css
@import "tailwindcss";
```

- Tailwind v4 utilise une seule directive d'import pour charger toutes les classes utilitaires.
- Le CLI Tailwind lit ce fichier et génère `output.css` avec toutes les classes utilisées dans le projet.

---

# Fichier 18 : `package.json`

## 1. Rôle du fichier

**Configuration du projet Node.js** — principalement pour Tailwind CSS.

---

```json
{
  "dependencies": {
    "@tailwindcss/cli": "^4.2.4"
  },
  "devDependencies": {
    "tailwindcss": "^4.3.0"
  }
}
```

- `dependencies` : packages nécessaires en production.
- `devDependencies` : packages nécessaires seulement en développement.
- `^4.2.4` : version 4.2.4 ou plus récente (compatible), mais pas 5.x.

---

# Flux global de l'application

## Scénario 1 : Un étudiant s'inscrit

```
1. /public/index.php         → Page d'accueil, bouton "Commencer"
2. /public/register.php      → Choisit "Étudiant"
3. /public/register_etudiant.php → Remplit le formulaire
4. /public/traitement_register.php → Validation + insertion DB
5. /public/accueil_etudiant.php    → Tableau de bord
```

## Scénario 2 : Un utilisateur se connecte

```
1. /public/login.php              → Formulaire de connexion
2. /public/traitement_login.php   → Vérification DB + création session
3. → Redirection vers accueil_etudiant.php ou accueil_etablissement.php
```

## Scénario 3 : Protection des pages

```
accueil_etudiant.php appelle check_auth() + check_role("etudiant")
→ Si non connecté : redirect vers index.php
→ Si mauvais rôle : redirect vers index.php
→ Sinon : affichage de la page
```

---

# Concepts clés récapitulatifs


| Concept                 | Explication simple                                                      |
| ----------------------- | ----------------------------------------------------------------------- |
| **Session PHP**         | Mémoire temporaire côté serveur liée à un navigateur via un cookie |
| **Requête préparée** | Requête SQL avec des`?` pour éviter les injections SQL                |
| **Password hashing**    | Transformation du mot de passe en code illisible stocké en DB          |
| **Flash message**       | Message stocké en session, affiché une seule fois puis supprimé      |
| **Clé étrangère**    | Champ qui référence la clé primaire d'une autre table                |
| **PDO**                 | Couche d'abstraction PHP pour parler à plusieurs types de DB           |
| **Tailwind CSS**        | Framework CSS basé sur des classes utilitaires                         |
| **LEFT JOIN**           | Jointure SQL qui garde toutes les lignes de la table gauche             |
| **AUTO_INCREMENT**      | Compteur automatique pour les identifiants uniques                      |
| **ENUM**                | Type SQL qui restreint les valeurs possibles à une liste               |
