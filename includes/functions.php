<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/starter_drugs.php';

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

// Fetch every drug for the current user, alphabetically.
function get_all_drugs()
{
    $stmt = get_db()->prepare('SELECT * FROM drugs WHERE user_id = ? ORDER BY name ASC');
    $stmt->execute([current_user_id()]);
    return $stmt->fetchAll();
}

// Fetch a single drug by id (only if it belongs to the current user), or null.
function get_drug($id)
{
    $stmt = get_db()->prepare('SELECT * FROM drugs WHERE id = ? AND user_id = ?');
    $stmt->execute([$id, current_user_id()]);
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
            'INSERT INTO drugs (user_id, name, strength, unit_type, morning, afternoon, evening, night, card, pack, pack_photo, pill_photo)
             VALUES (:user_id, :name, :strength, :unit_type, :morning, :afternoon, :evening, :night, :card, :pack, :pack_photo, :pill_photo)'
        );
        $stmt->execute(drug_params($data) + [':user_id' => current_user_id()]);
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
                name = :name, strength = :strength, unit_type = :unit_type,
                morning = :morning, afternoon = :afternoon, evening = :evening, night = :night,
                card = :card, pack = :pack, pack_photo = :pack_photo, pill_photo = :pill_photo
             WHERE id = :id AND user_id = :user_id'
        );
        $stmt->execute(drug_params($data) + [':id' => (int) $id, ':user_id' => current_user_id()]);
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
    $unit = ($data['unit_type'] ?? 'blister') === 'bottle' ? 'bottle' : 'blister';
    return [
        ':name'       => trim($data['name']),
        ':strength'   => ($data['strength'] === '' ? null : trim($data['strength'])),
        ':unit_type'  => $unit,
        ':morning'    => (int) $data['morning'],
        ':afternoon'  => (int) $data['afternoon'],
        ':evening'    => (int) $data['evening'],
        ':night'      => (int) $data['night'],
        // A bottle has no cards; keep card null for bottles.
        ':card'       => $unit === 'bottle' ? null : nullable_int($data['card']),
        ':pack'       => nullable_int($data['pack']),
        ':pack_photo' => ($data['pack_photo'] ?? null) ?: null,
        ':pill_photo' => ($data['pill_photo'] ?? null) ?: null,
    ];
}

// Work out what to buy for a drug over a number of days, given tablets already in hand.
function purchase_plan(array $drug, $days, $remaining = 0)
{
    $needed = (int) $days * per_day($drug);
    $to_buy = max(0, $needed - (int) $remaining);

    $plan = [
        'needed'    => $needed,
        'to_buy'    => $to_buy,
        'unit_type' => $drug['unit_type'],
        'cards'     => null,
        'packs'     => null,
        'bottles'   => null,
        'share_qty' => $to_buy . ' tablets',
    ];

    if ($drug['unit_type'] === 'bottle') {
        if ($drug['pack']) {
            $plan['bottles']   = (int) ceil($to_buy / $drug['pack']);
            $plan['share_qty'] = $plan['bottles'] . ' bottle' . ($plan['bottles'] === 1 ? '' : 's');
        }
    } else {
        if ($drug['card']) {
            $plan['cards']     = (int) ceil($to_buy / $drug['card']);
            $plan['share_qty'] = $plan['cards'] . ' card' . ($plan['cards'] === 1 ? '' : 's');
        }
        if ($drug['pack']) {
            $plan['packs'] = (int) ceil($to_buy / $drug['pack']);
            if ($plan['cards'] === null) {
                $plan['share_qty'] = $plan['packs'] . ' pack' . ($plan['packs'] === 1 ? '' : 's');
            }
        }
    }

    return $plan;
}

// Delete a drug by id (only if it belongs to the current user).
function delete_drug($id)
{
    return get_db()->prepare('DELETE FROM drugs WHERE id = ? AND user_id = ?')
        ->execute([(int) $id, current_user_id()]);
}

// Copy the starter sample drugs into a new account.
function seed_user_drugs($user_id)
{
    $stmt = get_db()->prepare(
        'INSERT INTO drugs (user_id, name, strength, unit_type, morning, afternoon, evening, night, card, pack)
         VALUES (:user_id, :name, :strength, :unit_type, :morning, :afternoon, :evening, :night, :card, :pack)'
    );
    foreach (starter_drugs() as $d) {
        $stmt->execute([
            ':user_id'   => (int) $user_id,
            ':name'      => $d['name'],
            ':strength'  => $d['strength'] !== '' ? $d['strength'] : null,
            ':unit_type' => $d['unit_type'],
            ':morning'   => $d['morning'],
            ':afternoon' => $d['afternoon'],
            ':evening'   => $d['evening'],
            ':night'     => $d['night'],
            ':card'      => $d['card'],
            ':pack'      => $d['pack'],
        ]);
    }
}

define('UPLOAD_DIR', __DIR__ . '/../uploads');

// Handle an optional image upload. Returns the new filename, or the existing one
// if nothing valid was uploaded. Validates that the file is a real image.
function handle_image_upload($field, $existing = null)
{
    if (empty($_FILES[$field]) || $_FILES[$field]['error'] === UPLOAD_ERR_NO_FILE) {
        return $existing;
    }

    $file = $_FILES[$field];
    if ($file['error'] !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        return $existing; // ignore errors and oversize files (> 5 MB)
    }

    $info = @getimagesize($file['tmp_name']);
    $allowed = ['image/jpeg' => 'jpg', 'image/png' => 'png', 'image/webp' => 'webp', 'image/gif' => 'gif'];
    if ($info === false || !isset($allowed[$info['mime']])) {
        return $existing; // not a recognised image
    }

    if (!is_dir(UPLOAD_DIR)) {
        @mkdir(UPLOAD_DIR, 0755, true);
    }

    $name = $field . '_' . bin2hex(random_bytes(8)) . '.' . $allowed[$info['mime']];
    if (!move_uploaded_file($file['tmp_name'], UPLOAD_DIR . '/' . $name)) {
        return $existing;
    }

    delete_upload($existing);
    return $name;
}

// Remove an uploaded file from disk if it exists.
function delete_upload($filename)
{
    if ($filename && is_file(UPLOAD_DIR . '/' . $filename)) {
        @unlink(UPLOAD_DIR . '/' . $filename);
    }
}

// Establish the session and current user now that all helpers are defined.
auth_bootstrap();
