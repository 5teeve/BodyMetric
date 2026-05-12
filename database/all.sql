CREATE DATABASE body_metric_db;

USE body_metric_db;

-- Table users : id, nom, prenom, email, mdp, genre, taille, poids, imc, wallet, is_gold
CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255),
    email VARCHAR(255) NOT NULL UNIQUE,
    mdp VARCHAR(255) NOT NULL,
    genre ENUM('M', 'F', 'Other') DEFAULT 'Other',
    taille DECIMAL(5, 2) DEFAULT NULL, -- cm
    poids DECIMAL(6, 2) DEFAULT NULL, -- kg
    imc DECIMAL(4, 2) DEFAULT NULL,
    wallet DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    is_gold TINYINT(1) NOT NULL DEFAULT 0,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO users (nom, prenom, email, mdp, genre, taille, poids, imc, wallet, is_gold) VALUES
('Dupont',  'Jean',   'jean.dupont@gmail.com',   '$2b$10$eOI89FJ9SaY00o/oDExL.e0E6SWD4V3yEZSv3Y8FVSP0VBb3ZpbFa', 'M',     178.00, 75.00, 23.68, 500.00,  0),
('Martin',  'Sophie', 'sophie.martin@gmail.com', '$2b$10$eOI89FJ9SaY00o/oDExL.e0E6SWD4V3yEZSv3Y8FVSP0VBb3ZpbFa', 'F',     165.00, 58.00, 21.30, 1200.00, 1),
('Bernard', 'Lucas',  'lucas.bernard@gmail.com', '$2b$10$eOI89FJ9SaY00o/oDExL.e0E6SWD4V3yEZSv3Y8FVSP0VBb3ZpbFa', 'M',     182.00, 90.00, 27.17, 0.00,    0),
('Lefevre', 'Emma',   'emma.lefevre@gmail.com',  '$2b$10$eOI89FJ9SaY00o/oDExL.e0E6SWD4V3yEZSv3Y8FVSP0VBb3ZpbFa', 'F',     160.00, 52.00, 20.31, 350.00,  0),
('Moreau',  'Thomas', 'thomas.moreau@gmail.com', '$2b$10$eOI89FJ9SaY00o/oDExL.e0E6SWD4V3yEZSv3Y8FVSP0VBb3ZpbFa', 'Other', 175.00, 80.00, 26.12, 2000.00, 1);

CREATE TABLE codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    montant DECIMAL(10, 2) NOT NULL,
    statut ENUM('actif', 'utilise') NOT NULL DEFAULT 'actif',
    user_id INT DEFAULT NULL,
    date_utilisation DATETIME DEFAULT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_codes_user FOREIGN KEY (user_id) REFERENCES users (id) ON DELETE SET NULL ON UPDATE CASCADE
);

-- Table regimes : id, nom, pct_viande, pct_poisson, pct_volaille, duree, prix, delta_poids
CREATE TABLE regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    pct_viande DECIMAL(5, 2) NOT NULL,
    pct_poisson DECIMAL(5, 2) NOT NULL,
    pct_volaille DECIMAL(5, 2) NOT NULL,
    duree INT NOT NULL COMMENT 'Durée en jours',
    prix DECIMAL(10, 2) NOT NULL,
    delta_poids DECIMAL(5, 2) DEFAULT NULL COMMENT 'Variation de poids estimée',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

INSERT INTO regimes 
(nom, pct_viande, pct_poisson, pct_volaille, duree, prix, delta_poids)
VALUES
('Regime Equilibre', 40.00, 30.00, 30.00, 30, 120000.00, -2.50),

('Regime Proteine Plus', 60.00, 20.00, 20.00, 45, 180000.00, -5.00),

('Regime Marin', 10.00, 70.00, 20.00, 60, 250000.00, -4.20),

('Regime Volaille Fit', 20.00, 10.00, 70.00, 30, 150000.00, -3.00),

('Regime Masse', 50.00, 25.00, 25.00, 90, 320000.00, 4.50);

-- Table activites : id, nom, type, intensite, duree_base, calories_min, objectif, description
CREATE TABLE IF NOT EXISTS activites (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    type ENUM('cardio', 'musculation', 'flexibilite', 'sport') NOT NULL DEFAULT 'cardio',
    intensite ENUM('faible', 'moderee', 'moyenne', 'elevee') NOT NULL DEFAULT 'moderee',
    duree_base INT NOT NULL COMMENT 'Duree estimée en minutes',
    calories_min INT NOT NULL COMMENT 'Calories brulees par minute',
    objectif ENUM('reduire', 'augmenter', 'maintenir') NOT NULL DEFAULT 'maintenir',
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des activités par défaut
INSERT INTO activites (nom, type, intensite, duree_base, calories_min, objectif, description) VALUES
-- Activités pour réduire le poids
('Marche rapide', 'cardio', 'moderee', 30, 5, 'reduire', 'Marche à allure soutenue pour brûler des graisses'),
('Course à pied', 'cardio', 'elevee', 25, 10, 'reduire', 'Running modéré pour brûler des calories'),
('Corde à sauter', 'cardio', 'elevee', 15, 12, 'reduire', 'Exercice intense pour brûlage rapide'),
('Vélo', 'cardio', 'moyenne', 45, 6, 'reduire', 'Cyclisme pour endurance et brûlage de graisses'),
('Natation', 'cardio', 'moyenne', 30, 8, 'reduire', 'Sport complet sans impact sur les articulations'),
('HIIT', 'cardio', 'elevee', 20, 11, 'reduire', 'Entraînement fractionné haute intensité'),

-- Activités pour augmenter le poids/masse musculaire
('Renforcement musculaire', 'musculation', 'moyenne', 40, 4, 'augmenter', 'Exercices avec charges pour gagner en masse'),
('Musculation intense', 'musculation', 'elevee', 50, 5, 'augmenter', 'Entraînement force pour hypertrophie'),
('CrossFit', 'sport', 'elevee', 45, 9, 'augmenter', 'Entraînement varié haute intensité'),
('Powerlifting', 'musculation', 'elevee', 60, 3, 'augmenter', 'Travail force maximale'),
('Street workout', 'musculation', 'moyenne', 35, 5, 'augmenter', 'Exercices au poids du corps'),

-- Activités pour maintenir
('Yoga', 'flexibilite', 'faible', 45, 3, 'maintenir', 'Renforcement doux et souplesse'),
('Pilates', 'flexibilite', 'moderee', 40, 4, 'maintenir', 'Renforcement du core et posture'),
('Stretching', 'flexibilite', 'faible', 20, 2, 'maintenir', 'Étirements pour récupération'),
('Marche tranquille', 'cardio', 'faible', 30, 3, 'maintenir', 'Activité légère pour bien-être'),
('Tai Chi', 'flexibilite', 'faible', 30, 3, 'maintenir', 'Gymnastique douce chinoise'),
('Aquagym', 'cardio', 'moderee', 45, 5, 'maintenir', 'Gymnastique en milieu aquatique');

-- Add objectif column to users table if it doesn't exist
ALTER TABLE users ADD COLUMN objectif VARCHAR(50) DEFAULT NULL AFTER is_gold;

-- Table parametres : clé/valeur pour les paramètres configurables
CREATE TABLE IF NOT EXISTS parametres (
    id INT PRIMARY KEY AUTO_INCREMENT,
    cle VARCHAR(100) NOT NULL UNIQUE,
    valeur TEXT NOT NULL,
    description TEXT,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Insertion des paramètres par défaut
INSERT INTO parametres (cle, valeur, description) VALUES
('prix_gold', '100000', 'Prix de l\'option Gold (en Ariary)'),
('imc_seuil_maigreur', '18.5', 'Seuil IMC pour la maigreur'),
('imc_seuil_surpoids', '25', 'Seuil IMC pour le surpoids'),
('imc_seuil_obesite', '30', 'Seuil IMC pour l\'obésité'),
('remise_gold_pourcent', '15', 'Pourcentage de remise Gold sur les régimes (%)');



CREATE TABLE IF NOT EXISTS regimes_achetes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    regime_id INT NOT NULL,
    prix_paye DECIMAL(10,2) NOT NULL,
    date_achat TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    duree_jours INT DEFAULT 30,
    date_fin DATE,
    status ENUM('actif', 'termine', 'annule') DEFAULT 'actif',
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (regime_id) REFERENCES regimes(id) ON DELETE CASCADE,
    UNIQUE KEY unique_user_regime (user_id, regime_id),
    INDEX idx_user_id (user_id),
    INDEX idx_regime_id (regime_id),
    INDEX idx_status (status)
);

-- Index pour les recherches fréquentes
CREATE INDEX idx_user_status ON regimes_achetes(user_id, status);
CREATE INDEX idx_date_achat ON regimes_achetes(date_achat);
