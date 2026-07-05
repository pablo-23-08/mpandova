DROP DATABASE IF EXISTS mpandova_db;

CREATE DATABASE IF NOT EXISTS mpandova_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE mpandova_db;

CREATE TABLE session ( 
    id_session VARCHAR(128) PRIMARY KEY,
    id_utilisateur INT UNSIGNED NOT NULL, 
    role ENUM('etudiant', 'etablissement') NOT NULL, 
    initial INT UNSIGNED NOT NULL );

CREATE TABLE utilisateur (
    id_utilisateur INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe_hash VARCHAR(255) NOT NULL,
    role ENUM('etudiant', 'etablissement') NOT NULL
);

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

CREATE TABLE filiere (
    id_filiere INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL
);

CREATE TABLE debouche (
    id_debouche INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(150) NOT NULL,
    description TEXT DEFAULT NULL
);

CREATE TABLE mener (
    id_filiere INT UNSIGNED NOT NULL,
    id_debouche INT UNSIGNED NOT NULL,

    niveau_etude VARCHAR(100) NOT NULL,

    PRIMARY KEY (id_filiere, id_debouche),

    CONSTRAINT fk_mener_filiere
        FOREIGN KEY (id_filiere)
        REFERENCES filiere(id_filiere)
        ON DELETE CASCADE,

    CONSTRAINT fk_mener_debouche
        FOREIGN KEY (id_debouche)
        REFERENCES debouche(id_debouche)
        ON DELETE CASCADE
);

CREATE TABLE offre_filiere (
    id_offre_filiere INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    frais_scolarite DECIMAL(10,2) DEFAULT 0,
    place_disponible INT DEFAULT 0,
    duree_formation VARCHAR(100) DEFAULT NULL,

    id_etablissement INT UNSIGNED NOT NULL,
    id_filiere INT UNSIGNED NOT NULL,

    CONSTRAINT fk_offre_etablissement
        FOREIGN KEY (id_etablissement)
        REFERENCES etablissement(id_etablissement)
        ON DELETE CASCADE,

    CONSTRAINT fk_offre_filiere
        FOREIGN KEY (id_filiere)
        REFERENCES filiere(id_filiere)
        ON DELETE CASCADE
);

CREATE TABLE condition_acces (
    id_condition_acces INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    diplome_requis VARCHAR(150) DEFAULT NULL,
    age_max INT DEFAULT NULL,

    serie_bac ENUM('A','C','D','L','OSE','S') DEFAULT NULL,

    annee_bac YEAR DEFAULT NULL,

    moyenne_bac DECIMAL(4,2) DEFAULT NULL,

    id_offre_filiere INT UNSIGNED NOT NULL UNIQUE,

    CONSTRAINT fk_condition_offre
        FOREIGN KEY (id_offre_filiere)
        REFERENCES offre_filiere(id_offre_filiere)
        ON DELETE CASCADE
);

CREATE TABLE diplome (
    id_diplome INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    nom VARCHAR(150) NOT NULL,
    annee_obtention YEAR NULL,

    id_etudiant INT UNSIGNED NOT NULL,

    CONSTRAINT fk_diplome_etudiant
        FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant)
        ON DELETE CASCADE
);

CREATE TABLE bac (
    id_bac INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    serie ENUM('A','C','D','L','OSE','S') NOT NULL,

    moyenne DECIMAL(4,2) DEFAULT NULL,

    mention VARCHAR(50) DEFAULT NULL,

    id_diplome INT UNSIGNED NOT NULL UNIQUE,

    CONSTRAINT fk_bac_diplome
        FOREIGN KEY (id_diplome)
        REFERENCES diplome(id_diplome)
        ON DELETE CASCADE
);

CREATE TABLE recommandation (
    id_recommandation INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    score DECIMAL(5,2) DEFAULT 0,

    date_recommandation DATETIME
        DEFAULT CURRENT_TIMESTAMP,

    justification TEXT DEFAULT NULL,

    id_etudiant INT UNSIGNED NOT NULL,
    id_offre_filiere INT UNSIGNED NOT NULL,

    CONSTRAINT fk_recommandation_etudiant
        FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant)
        ON DELETE CASCADE,

    CONSTRAINT fk_recommandation_offre
        FOREIGN KEY (id_offre_filiere)
        REFERENCES offre_filiere(id_offre_filiere)
        ON DELETE CASCADE
);

CREATE TABLE consulter (
    id_etudiant INT UNSIGNED NOT NULL,
    id_offre_filiere INT UNSIGNED NOT NULL,

    date_consultation DATETIME
        DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id_etudiant, id_offre_filiere),

    CONSTRAINT fk_consulter_etudiant
        FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant)
        ON DELETE CASCADE,

    CONSTRAINT fk_consulter_offre
        FOREIGN KEY (id_offre_filiere)
        REFERENCES offre_filiere(id_offre_filiere)
        ON DELETE CASCADE
);

-- Table candidature : une demande d'admission d'un étudiant pour une offre de filière
CREATE TABLE candidature (
    id_candidature   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,

    -- Statut du dossier : 4 états possibles
    statut ENUM('en_attente', 'acceptee', 'refusee', 'annulee')
        NOT NULL DEFAULT 'en_attente',

    -- Date de la candidature (remplie automatiquement par MySQL)
    date_candidature DATETIME DEFAULT CURRENT_TIMESTAMP,

    -- Date à laquelle l'établissement a traité la demande (null tant que non traitée)
    date_traitement  DATETIME DEFAULT NULL,

    -- Message optionnel de l'étudiant lors de sa candidature
    message TEXT DEFAULT NULL,

    -- Clés étrangères
    id_etudiant      INT UNSIGNED NOT NULL,
    id_offre_filiere INT UNSIGNED NOT NULL,

    CONSTRAINT fk_candidature_etudiant
        FOREIGN KEY (id_etudiant)
        REFERENCES etudiant(id_etudiant)
        ON DELETE CASCADE,

    CONSTRAINT fk_candidature_offre
        FOREIGN KEY (id_offre_filiere)
        REFERENCES offre_filiere(id_offre_filiere)
        ON DELETE CASCADE,

    -- Un étudiant ne peut postuler qu'une seule fois à la même offre
    UNIQUE KEY unique_candidature (id_etudiant, id_offre_filiere)
);

-- Filières initiales (exemple, à adapter selon le contexte malgache)
INSERT INTO filiere (nom, description) VALUES
('Informatique',       'Formation en développement logiciel, réseaux et systèmes'),
('Médecine',           'Formation médicale générale (6 ans)'),
('Droit',              'Études juridiques et sciences politiques'),
('Économie',           'Sciences économiques et gestion'),
('Génie Civil',        'Construction, bâtiment et travaux publics'),
('Électronique',       'Électronique, électrotechnique et automatisme'),
('Gestion',            'Management, comptabilité et finance d\'entreprise'),
('Lettres',            'Littérature, linguistique et sciences humaines'),
('Agronomie',          'Agriculture, élevage et développement rural'),
('Tourisme',           'Hôtellerie, tourisme et gestion d\'établissements');