<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $existing = get_drug($id);
    if (!$existing) {
        http_response_code(404);
        exit('Drug not found.');
    }

    $v = [
        'name'      => $_POST['name']      ?? '',
        'strength'  => $_POST['strength']  ?? '',
        'unit_type' => ($_POST['unit_type'] ?? 'blister') === 'bottle' ? 'bottle' : 'blister',
        'morning'   => $_POST['morning']   ?? '0',
        'afternoon' => $_POST['afternoon'] ?? '0',
        'evening'   => $_POST['evening']   ?? '0',
        'night'     => $_POST['night']     ?? '0',
    ];
    if ($v['unit_type'] === 'bottle') {
        $v['card'] = '';
        $v['pack'] = $_POST['bottle_size'] ?? '';
    } else {
        $v['card'] = $_POST['card'] ?? '';
        $v['pack'] = $_POST['pack'] ?? '';
    }
    // Keep existing photos unless a new one is uploaded.
    $v['pack_photo'] = handle_image_upload('pack_photo', $existing['pack_photo']);
    $v['pill_photo'] = handle_image_upload('pill_photo', $existing['pill_photo']);

    if (trim($v['name']) === '') {
        $error = 'Drug name is required.';
    } else {
        $result = update_drug($id, $v);
        if ($result === true) {
            header('Location: manage.php');
            exit;
        }
        $error = $result;
    }
    $drug_id = $id;
} else {
    $id = $_GET['id'] ?? '';
    $drug = get_drug($id);
    if (!$drug) {
        http_response_code(404);
        exit('Drug not found.');
    }
    $drug_id = $drug['id'];
    $v = [
        'name'       => $drug['name'],
        'strength'   => $drug['strength'],
        'unit_type'  => $drug['unit_type'],
        'morning'    => $drug['morning'],
        'afternoon'  => $drug['afternoon'],
        'evening'    => $drug['evening'],
        'night'      => $drug['night'],
        'card'       => $drug['card'],
        'pack'       => $drug['pack'],
        'pack_photo' => $drug['pack_photo'],
        'pill_photo' => $drug['pill_photo'],
    ];
}

$title = 'Edit Drug';
require __DIR__ . '/includes/header.php';
$submit_label = 'Save Changes';
$form_action = 'update-drug.php';
require __DIR__ . '/includes/_drug_form.php';
require __DIR__ . '/includes/footer.php';
