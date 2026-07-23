<?php
require_once __DIR__ . '/includes/functions.php';

$error = '';
$v = ['name' => '', 'strength' => '', 'morning' => '0', 'afternoon' => '0',
      'evening' => '0', 'night' => '0', 'card' => '', 'pack' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($v as $k => $_) {
        $v[$k] = $_POST[$k] ?? $v[$k];
    }

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
