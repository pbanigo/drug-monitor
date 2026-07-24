<?php
require_once __DIR__ . '/includes/functions.php';
auth_logout();
header('Location: index.php');
exit;
