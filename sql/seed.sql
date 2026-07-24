-- Sample drug list. Import after schema.sql to pre-populate the table.
-- Vitamins/supplements come in bottles of 100; the rest are blister packs.

INSERT INTO drugs (name, strength, unit_type, morning, afternoon, evening, night, card, pack) VALUES
('TreviaMet',   '50/500', 'blister', 1, 0, 1, 0, NULL, NULL),
('Metformin',   '500mg',  'blister', 1, 0, 1, 0, NULL, NULL),
('Vitamin C',   '100mg',  'bottle',  2, 2, 2, 0, NULL, 100),
('D3',          '5000iu', 'bottle',  0, 1, 0, 0, NULL, 100),
('B12',         NULL,     'bottle',  0, 1, 0, 0, NULL, 100),
('Calcium',     NULL,     'bottle',  0, 1, 0, 0, NULL, 100),
('Simvastatin', '10mg',   'blister', 0, 0, 0, 1, NULL, NULL),
('Vasoprin',    NULL,     'blister', 0, 0, 0, 1, NULL, NULL)
ON DUPLICATE KEY UPDATE
    strength  = VALUES(strength),
    unit_type = VALUES(unit_type),
    morning   = VALUES(morning),
    afternoon = VALUES(afternoon),
    evening   = VALUES(evening),
    night     = VALUES(night),
    card      = VALUES(card),
    pack      = VALUES(pack);
