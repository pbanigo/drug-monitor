-- Drug Monitor database schema (MySQL / MariaDB)
-- Import this once via phpMyAdmin or the MySQL CLI to create the table.

CREATE TABLE IF NOT EXISTS drugs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL UNIQUE,
    strength   VARCHAR(100) DEFAULT NULL,   -- e.g. 50/500, 500mg, 5000iu
    morning    INT NOT NULL DEFAULT 0,      -- tablets taken in the morning
    afternoon  INT NOT NULL DEFAULT 0,      -- tablets taken in the afternoon
    evening    INT NOT NULL DEFAULT 0,      -- tablets taken in the evening
    night      INT NOT NULL DEFAULT 0,      -- tablets taken at night
    card       INT DEFAULT NULL,            -- tablets per card (for the purchase calculator)
    pack       INT DEFAULT NULL,            -- tablets per pack (for the purchase calculator)
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
