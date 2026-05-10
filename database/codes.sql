CREATE TABLE IF NOT EXISTS codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(16) NOT NULL UNIQUE,
    montant DECIMAL(8,2) NOT NULL,
    statut ENUM('actif','utilise') NOT NULL DEFAULT 'actif',
    user_id INT NULL,
    date_utilisation DATETIME NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_codes_code (code),
    INDEX idx_codes_user_id (user_id),
    CONSTRAINT fk_codes_user_id FOREIGN KEY (user_id) REFERENCES users(id)
        ON UPDATE CASCADE
        ON DELETE SET NULL
);