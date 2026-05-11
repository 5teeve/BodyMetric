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
