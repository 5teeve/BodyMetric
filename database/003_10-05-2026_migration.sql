-- Table regimes : id, nom, pct_viande, pct_poisson, pct_volaille, duree, prix, delta_poids
CREATE OR REPLACE TABLE regimes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nom VARCHAR(100) NOT NULL,
    pct_viande DECIMAL(5, 2) NOT NULL,
    pct_poisson DECIMAL(5, 2) NOT NULL
    pct_volaille DECIMAL(5, 2) NOT NULL,
    duree INT NOT NULL COMMENT 'Durée en jours',
    prix DECIMAL(10, 2) NOT NULL,
    delta_poids DECIMAL(5, 2) DEFAULT NULL COMMENT 'Variation de poids estimée',
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);