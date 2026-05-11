CREATE DATABASE body_metric_db;
USE body_metric_db;

-- Table users : id, nom, prenom, email, mdp, genre, taille, poids, imc, wallet, is_gold
CREATE OR REPLACE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    genre ENUM('M','F','Other') DEFAULT 'Other',
    taille DECIMAL(5,2) DEFAULT NULL, -- cm
    poids DECIMAL(6,2) DEFAULT NULL,  -- kg
    imc DECIMAL(4,2) DEFAULT NULL,
    wallet DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    is_gold TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);