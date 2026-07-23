<?php
require_once __DIR__ . '/db.php';

// Escape output for safe HTML rendering.
function e($value)
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

// Total tablets taken per day.
function per_day(array $drug)
{
    return (int) $drug['morning'] + (int) $drug['afternoon']
         + (int) $drug['evening'] + (int) $drug['night'];
}

// The four times of day, with labels and icons, for rendering schedule chips.
function time_slots()
{
    return [
        'morning'   => ['label' => 'Morning',   'icon' => 'fa-sun'],
        'afternoon' => ['label' => 'Afternoon', 'icon' => 'fa-cloud-sun'],
        'evening'   => ['label' => 'Evening',   'icon' => 'fa-cloud-moon'],
        'night'     => ['label' => 'Night',     'icon' => 'fa-moon'],
    ];
}

// Render the schedule as coloured chips (morning / afternoon / evening / night).
function schedule_chips_html(array $drug)
{
    $html = '<span class="chips">';
    $any = false;
    foreach (time_slots() as $key => $slot) {
        $count = (int) $drug[$key];
        if ($count > 0) {
            $any = true;
            $html .= '<span class="chip ' . $key . '">'
                   . '<i class="fas ' . $slot['icon'] . '"></i>' . e($slot['label'])
                   . '<span class="chip-count">' . $count . '</span></span>';
        }
    }
    if (!$any) {
        $html .= '<span class="chip empty">Not scheduled</span>';
    }
    return $html . '</span>';
}

// Human-readable schedule, e.g. "1 morning, 1 evening".
function dosage_summary(array $drug)
{
    $parts = [];
    foreach (time_slots() as $key => $slot) {
        $count = (int) $drug[$key];
        if ($count > 0) {
            $parts[] = $count . ' ' . strtolower($slot['label']);
        }
    }
    return $parts ? implode(', ', $parts) : 'Not scheduled';
}

// Fetch every drug, alphabetically.
function get_all_drugs()
{
    return get_db()->query('SELECT * FROM drugs ORDER BY name ASC')->fetchAll();
}

// Fetch a single drug by id, or null if not found.
function get_drug($id)
{
    $stmt = get_db()->prepare('SELECT * FROM drugs WHERE id = ?');
    $stmt->execute([$id]);
    return $stmt->fetch() ?: null;
}

// Normalise optional integer fields (card / pack) to int or null.
function nullable_int($value)
{
    return ($value === '' || $value === null) ? null : (int) $value;
}

// Insert a new drug. Returns true on success, or an error message string.
function create_drug(array $data)
{
    try {
        $stmt = get_db()->prepare(
            'INSERT INTO drugs (name, strength, morning, afternoon, evening, night, card, pack)
             VALUES (:name, :strength, :morning, :afternoon, :evening, :night, :card, :pack)'
        );
        $stmt->execute(drug_params($data));
        return true;
    } catch (PDOException $e) {
        return $e->getCode() === '23000'
            ? 'A drug with that name already exists.'
            : 'There was an error while adding the drug.';
    }
}

// Update an existing drug. Returns true on success, or an error message string.
function update_drug($id, array $data)
{
    try {
        $stmt = get_db()->prepare(
            'UPDATE drugs SET
                name = :name, strength = :strength,
                morning = :morning, afternoon = :afternoon, evening = :evening, night = :night,
                card = :card, pack = :pack
             WHERE id = :id'
        );
        $stmt->execute(drug_params($data) + [':id' => (int) $id]);
        return true;
    } catch (PDOException $e) {
        return $e->getCode() === '23000'
            ? 'A drug with that name already exists.'
            : 'Error in updating drug information.';
    }
}

// Build the bound parameters shared by insert and update.
function drug_params(array $data)
{
    return [
        ':name'      => trim($data['name']),
        ':strength'  => ($data['strength'] === '' ? null : trim($data['strength'])),
        ':morning'   => (int) $data['morning'],
        ':afternoon' => (int) $data['afternoon'],
        ':evening'   => (int) $data['evening'],
        ':night'     => (int) $data['night'],
        ':card'      => nullable_int($data['card']),
        ':pack'      => nullable_int($data['pack']),
    ];
}

// Delete a drug by id.
function delete_drug($id)
{
    return get_db()->prepare('DELETE FROM drugs WHERE id = ?')->execute([(int) $id]);
}
