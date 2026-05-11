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
