<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = $_POST['id'] ?? '';
    $v = [
        'name'      => $_POST['name']      ?? '',
        'strength'  => $_POST['strength']  ?? '',
        'morning'   => $_POST['morning']   ?? '0',
        'afternoon' => $_POST['afternoon'] ?? '0',
        'evening'   => $_POST['evening']   ?? '0',
        'night'     => $_POST['night']     ?? '0',
        'card'      => $_POST['card']      ?? '',
        'pack'      => $_POST['pack']      ?? '',
    ];

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
        'name'      => $drug['name'],
        'strength'  => $drug['strength'],
        'morning'   => $drug['morning'],
        'afternoon' => $drug['afternoon'],
        'evening'   => $drug['evening'],
        'night'     => $drug['night'],
        'card'      => $drug['card'],
        'pack'      => $drug['pack'],
    ];
}

$title = 'Edit Drug';
require __DIR__ . '/includes/header.php';
$submit_label = 'Save Changes';
$form_action = 'update-drug.php';
require __DIR__ . '/includes/_drug_form.php';
require __DIR__ . '/includes/footer.php';
