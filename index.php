<?php
require_once __DIR__ . '/includes/functions.php';
$drugs = get_all_drugs();
$title = 'Home';
require __DIR__ . '/includes/header.php';
?>
<section class="hero">
  <h1>Stay on top of every dose</h1>
  <p>Plan and organise prescription drugs: track what to take and when, and work out exactly how much to buy.</p>
</section>

<div class="action-grid">
  <a class="action-card" href="dosage.php">
    <div class="ac-icon"><i class="fas fa-clock"></i></div>
    <h3>Check Dosage</h3>
    <p>See the daily schedule for every drug at a glance.</p>
  </a>
  <a class="action-card" href="purchase.php">
    <div class="ac-icon"><i class="fas fa-cart-shopping"></i></div>
    <h3>Plan a Purchase</h3>
    <p>Calculate how many cards and packs to buy for any period.</p>
  </a>
  <a class="action-card" href="manage.php">
    <div class="ac-icon"><i class="fas fa-pen-to-square"></i></div>
    <h3>Manage Drugs</h3>
    <p><?= count($drugs) ?> <?= count($drugs) === 1 ? 'drug' : 'drugs' ?> on record. Add, edit or remove.</p>
  </a>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
