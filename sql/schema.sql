-- Drug Monitor database schema (MySQL / MariaDB)
-- Import this once via phpMyAdmin or the MySQL CLI to create the tables.

CREATE TABLE IF NOT EXISTS users (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    username      VARCHAR(50)  UNIQUE DEFAULT NULL,  -- null until a guest upgrades
    password_hash VARCHAR(255) DEFAULT NULL,         -- null for guests
    guest_token   CHAR(64)     UNIQUE DEFAULT NULL,  -- cookie token for anonymous users
    created_at    TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    last_seen     TIMESTAMP NULL DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS drugs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    user_id    INT NOT NULL,
    name       VARCHAR(255) NOT NULL,
    strength   VARCHAR(100) DEFAULT NULL,   -- e.g. 50/500, 500mg, 5000iu
    unit_type  ENUM('blister','bottle') NOT NULL DEFAULT 'blister',
    morning    INT NOT NULL DEFAULT 0,      -- tablets taken in the morning
    afternoon  INT NOT NULL DEFAULT 0,      -- tablets taken in the afternoon
    evening    INT NOT NULL DEFAULT 0,      -- tablets taken in the evening
    night      INT NOT NULL DEFAULT 0,      -- tablets taken at night
    card       INT DEFAULT NULL,            -- tablets per card (blister only)
    pack       INT DEFAULT NULL,            -- tablets per pack, or per bottle for bottle type
    pack_photo VARCHAR(255) DEFAULT NULL,   -- filename of the drug pack photo
    pill_photo VARCHAR(255) DEFAULT NULL,   -- filename of the pill photo
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY uniq_user_name (user_id, name),
    INDEX idx_user (user_id),
    CONSTRAINT fk_drugs_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
