<?php
require_once __DIR__ . '/includes/functions.php';

// Only accept POST (with a valid CSRF token) so drugs can't be deleted via a plain link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check() && !empty($_POST['id'])) {
    $drug = get_drug($_POST['id']);
    if ($drug) {
        delete_upload($drug['pack_photo']);
        delete_upload($drug['pill_photo']);
        delete_drug($drug['id']);
    }
}

header('Location: manage.php');
exit;
