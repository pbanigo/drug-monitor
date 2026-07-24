-- Drug Monitor database schema (MySQL / MariaDB)
-- Import this once via phpMyAdmin or the MySQL CLI to create the table.

CREATE TABLE IF NOT EXISTS drugs (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    name       VARCHAR(255) NOT NULL UNIQUE,
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
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
