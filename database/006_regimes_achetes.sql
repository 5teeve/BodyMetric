-- Migration 006: Table regimes_achetes
-- Permet de stocker les régimes achetés par les utilisateurs

USE body_metric_db;

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
