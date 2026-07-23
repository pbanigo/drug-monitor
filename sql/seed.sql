-- Sample drug list. Import after schema.sql to pre-populate the table.
-- Columns: name, strength, morning, afternoon, evening, night

INSERT INTO drugs (name, strength, morning, afternoon, evening, night) VALUES
('TreviaMet',   '50/500', 1, 0, 1, 0),
('Metformin',   '500mg',  1, 0, 1, 0),
('Vitamin C',   '100mg',  2, 2, 2, 0),
('D3',          '5000iu', 0, 1, 0, 0),
('B12',         NULL,     0, 1, 0, 0),
('Calcium',     NULL,     0, 1, 0, 0),
('Simvastatin', '10mg',   0, 0, 0, 1),
('Vasoprin',    NULL,     0, 0, 0, 1)
ON DUPLICATE KEY UPDATE
    strength  = VALUES(strength),
    morning   = VALUES(morning),
    afternoon = VALUES(afternoon),
    evening   = VALUES(evening),
    night     = VALUES(night);
