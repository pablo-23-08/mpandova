CREATE DATABASE IF NOT EXISTS mpandova
    CHARACTER SET utf8mb4;

USE mpandova;

CREATE TABLE user (
    id_user   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    email     VARCHAR(100) NOT NULL UNIQUE,
    password  VARCHAR(255) NOT NULL,
    role      ENUM('etudiant', 'etablissement') NOT NULL
);

CREATE TABLE etudiant (
    id_etudiant       INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom               VARCHAR(100) NOT NULL,
    prenom            VARCHAR(100) NOT NULL,
    date_de_naissance DATE         DEFAULT NULL,
    serie_bac         ENUM('A','C','D','L','OSE','S') NOT NULL,
    id_user           INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_etudiant_user FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

CREATE TABLE etablissement (
    id_etablissement INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom              VARCHAR(100) NOT NULL,
    type             ENUM('universite','grande_ecole','institut_prive','lycee_technique','autre') NOT NULL,
    site_web         VARCHAR(100) DEFAULT NULL,
    id_user          INT UNSIGNED NOT NULL UNIQUE,
    CONSTRAINT fk_etablissement_user FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

CREATE TABLE location (
    id_etablissement INT UNSIGNED PRIMARY KEY,
    ville            VARCHAR(100) DEFAULT NULL,
    adresse          VARCHAR(100) DEFAULT NULL,
    CONSTRAINT fk_location_etablissement FOREIGN KEY (id_etablissement) REFERENCES etablissement(id_etablissement) ON DELETE CASCADE
);

CREATE TABLE bac (
    id_bac  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    serie   ENUM('A','C','D','L','OSE','S') NOT NULL,
    annee   YEAR  NOT NULL,
    moyenne FLOAT DEFAULT NULL
);

CREATE TABLE diplome (
    id_diplome  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    id_etudiant INT UNSIGNED NOT NULL,
    id_bac      INT UNSIGNED NOT NULL UNIQUE,
    annee       YEAR NOT NULL,
    CONSTRAINT fk_diplome_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    CONSTRAINT fk_diplome_bac      FOREIGN KEY (id_bac)      REFERENCES bac(id_bac)           ON DELETE CASCADE
);

CREATE TABLE filiere (
    id_filiere  INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    description VARCHAR(100) DEFAULT NULL
);

CREATE TABLE condition_admission (
    id_filiere   INT UNSIGNED NOT NULL PRIMARY KEY,
    serie_bac    ENUM('A','C','D','L','OSE','S') DEFAULT NULL,
    annee_bac    YEAR  DEFAULT NULL,
    moyenne_bac  FLOAT DEFAULT NULL,
    CONSTRAINT fk_condition_filiere FOREIGN KEY (id_filiere) REFERENCES filiere(id_filiere) ON DELETE CASCADE
);

CREATE TABLE debouche (
    id_debouche INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nom         VARCHAR(100) NOT NULL,
    description VARCHAR(100) DEFAULT NULL
);

CREATE TABLE mener (
    id_filiere   INT UNSIGNED NOT NULL,
    id_debouche  INT UNSIGNED NOT NULL,
    niveau_etude VARCHAR(100) NOT NULL,
    PRIMARY KEY (id_filiere, id_debouche),
    CONSTRAINT fk_mener_filiere  FOREIGN KEY (id_filiere)  REFERENCES filiere(id_filiere)   ON DELETE CASCADE,
    CONSTRAINT fk_mener_debouche FOREIGN KEY (id_debouche) REFERENCES debouche(id_debouche) ON DELETE CASCADE
);

CREATE TABLE proposer (
    id_etablissement INT UNSIGNED NOT NULL,
    id_filiere       INT UNSIGNED NOT NULL,
    PRIMARY KEY (id_etablissement, id_filiere),
    CONSTRAINT fk_proposer_etablissement FOREIGN KEY (id_etablissement) REFERENCES etablissement(id_etablissement) ON DELETE CASCADE,
    CONSTRAINT fk_proposer_filiere       FOREIGN KEY (id_filiere)       REFERENCES filiere(id_filiere)             ON DELETE CASCADE
);

CREATE TABLE consulter (
    id_etudiant INT UNSIGNED NOT NULL,
    id_filiere  INT UNSIGNED NOT NULL,
    date        DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (id_etudiant, id_filiere),
    CONSTRAINT fk_consulter_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    CONSTRAINT fk_consulter_filiere  FOREIGN KEY (id_filiere)  REFERENCES filiere(id_filiere)   ON DELETE CASCADE
);

CREATE TABLE recommander (
    id_etudiant INT UNSIGNED NOT NULL,
    id_filiere  INT UNSIGNED NOT NULL,
    score       FLOAT NOT NULL DEFAULT 0,
    PRIMARY KEY (id_etudiant, id_filiere),
    CONSTRAINT fk_recommander_etudiant FOREIGN KEY (id_etudiant) REFERENCES etudiant(id_etudiant) ON DELETE CASCADE,
    CONSTRAINT fk_recommander_filiere  FOREIGN KEY (id_filiere)  REFERENCES filiere(id_filiere)   ON DELETE CASCADE
);