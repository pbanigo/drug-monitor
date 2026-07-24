<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';
$v = ['name' => '', 'strength' => '', 'unit_type' => 'blister',
      'morning' => '0', 'afternoon' => '0', 'evening' => '0', 'night' => '0',
      'card' => '', 'pack' => '', 'pack_photo' => null, 'pill_photo' => null];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $v['name']      = $_POST['name']      ?? '';
    $v['strength']  = $_POST['strength']  ?? '';
    $v['unit_type'] = ($_POST['unit_type'] ?? 'blister') === 'bottle' ? 'bottle' : 'blister';
    foreach (['morning', 'afternoon', 'evening', 'night'] as $slot) {
        $v[$slot] = $_POST[$slot] ?? '0';
    }
    // Bottles use a single "tablets per bottle" field, stored in the pack column.
    if ($v['unit_type'] === 'bottle') {
        $v['card'] = '';
        $v['pack'] = $_POST['bottle_size'] ?? '';
    } else {
        $v['card'] = $_POST['card'] ?? '';
        $v['pack'] = $_POST['pack'] ?? '';
    }
    $v['pack_photo'] = handle_image_upload('pack_photo');
    $v['pill_photo'] = handle_image_upload('pill_photo');

    if (trim($v['name']) === '') {
        $error = 'Drug name is required.';
    } else {
        $result = create_drug($v);
        if ($result === true) {
            header('Location: manage.php');
            exit;
        }
        $error = $result;
    }
}

$title = 'Add Drug';
require __DIR__ . '/includes/header.php';
$submit_label = 'Add Drug';
$form_action = 'add-drug.php';
$drug_id = '';
require __DIR__ . '/includes/_drug_form.php';
require __DIR__ . '/includes/footer.php';
