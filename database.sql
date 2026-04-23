CREATE DATABASE mpandova;
USE mpandova;

-- TABLE USER
CREATE TABLE user (
    id_user INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('etudiant','etablissement') NOT NULL
);

-- TABLE ETUDIANT
CREATE TABLE etudiant (
    id_etudiant INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50),
    prenom VARCHAR(50),
    serie_bac VARCHAR(50),
    annee_bac INT,
    moyenne_bac FLOAT,
    id_user INT UNIQUE,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);

-- TABLE ETABLISSEMENT
CREATE TABLE etablissement (
    id_etablissement INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100),
    type VARCHAR(50),
    site_web VARCHAR(255),
    id_user INT UNIQUE,
    FOREIGN KEY (id_user) REFERENCES user(id_user) ON DELETE CASCADE
);