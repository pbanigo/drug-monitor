<?php
$title = isset($title) ? $title : 'Drug Monitor';
$current = basename($_SERVER['SCRIPT_NAME']);
function nav_active($page)
{
    global $current;
    return $current === $page ? ' class="active"' : '';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($title) ?> - Drug Monitor</title>
  <link rel="apple-touch-icon" sizes="180x180" href="assets/images/favicon/apple-touch-icon.png">
  <link rel="icon" type="image/png" sizes="32x32" href="assets/images/favicon/favicon-32x32.png">
  <link rel="icon" type="image/png" sizes="16x16" href="assets/images/favicon/favicon-16x16.png">
  <link rel="manifest" href="assets/images/favicon/site.webmanifest">
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
  <link rel="stylesheet" href="assets/css/styles.css">
</head>
<body>
  <div id="app">
    <nav class="site-nav">
      <div class="nav-inner">
        <a class="brand" href="index.php">
          <span class="brand-mark"><i class="fas fa-capsules"></i></span>
          Drug Monitor
        </a>
        <button class="nav-toggle" id="navToggle" aria-label="Toggle menu" aria-expanded="false">
          <i class="fas fa-bars"></i>
        </button>
        <div class="nav-links" id="navLinks">
          <a href="index.php"<?= nav_active('index.php') ?>>Home</a>
          <a href="manage.php"<?= nav_active('manage.php') ?>>Manage</a>
          <a href="dosage.php"<?= nav_active('dosage.php') ?>>Dosage</a>
          <a href="purchase.php"<?= nav_active('purchase.php') ?>>Purchase</a>
          <?php if (is_logged_in()): ?>
            <span class="nav-user"><i class="fas fa-user"></i> <?= e(current_user()['username']) ?></span>
            <a href="logout.php" class="nav-cta-ghost">Log out</a>
          <?php else: ?>
            <a href="login.php">Log in</a>
            <a href="register.php" class="nav-cta">Create account</a>
          <?php endif; ?>
        </div>
      </div>
    </nav>
    <?php if (is_guest() && $current !== 'register.php' && $current !== 'login.php'): ?>
    <div class="guest-banner no-print">
      <i class="fas fa-circle-info"></i>
      You are trying Drug Monitor as a guest. Your changes are saved on this device only.
      <a href="register.php">Create a free account</a> to keep them safely.
    </div>
    <?php endif; ?>
    <main id="main">
