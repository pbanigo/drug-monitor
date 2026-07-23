<?php
require_once __DIR__ . '/includes/functions.php';

// Only accept POST so drugs can't be deleted via a plain link.
if ($_SERVER['REQUEST_METHOD'] === 'POST' && !empty($_POST['id'])) {
    delete_drug($_POST['id']);
}

header('Location: manage.php');
exit;
