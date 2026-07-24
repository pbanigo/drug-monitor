<?php
// Sample drugs copied into every new account so the app is not empty to try.
// Users can edit or delete these freely.
function starter_drugs()
{
    return [
        ['name' => 'TreviaMet',   'strength' => '50/500', 'unit_type' => 'blister', 'morning' => 1, 'afternoon' => 0, 'evening' => 1, 'night' => 0, 'card' => 7,    'pack' => 35],
        ['name' => 'Metformin',   'strength' => '500mg',  'unit_type' => 'blister', 'morning' => 1, 'afternoon' => 0, 'evening' => 1, 'night' => 0, 'card' => 10,   'pack' => 90],
        ['name' => 'Vitamin C',   'strength' => '100mg',  'unit_type' => 'bottle',  'morning' => 2, 'afternoon' => 2, 'evening' => 2, 'night' => 0, 'card' => null, 'pack' => 100],
        ['name' => 'D3',          'strength' => '5000iu', 'unit_type' => 'bottle',  'morning' => 0, 'afternoon' => 1, 'evening' => 0, 'night' => 0, 'card' => null, 'pack' => 100],
        ['name' => 'B12',         'strength' => '',       'unit_type' => 'bottle',  'morning' => 0, 'afternoon' => 1, 'evening' => 0, 'night' => 0, 'card' => null, 'pack' => 100],
        ['name' => 'Calcium',     'strength' => '',       'unit_type' => 'bottle',  'morning' => 0, 'afternoon' => 1, 'evening' => 0, 'night' => 0, 'card' => null, 'pack' => 100],
        ['name' => 'Simvastatin', 'strength' => '20mg',   'unit_type' => 'blister', 'morning' => 0, 'afternoon' => 0, 'evening' => 1, 'night' => 0, 'card' => 14,   'pack' => 28],
        ['name' => 'Vasoprin',    'strength' => '',       'unit_type' => 'blister', 'morning' => 1, 'afternoon' => 0, 'evening' => 0, 'night' => 0, 'card' => 10,   'pack' => 100],
    ];
}
