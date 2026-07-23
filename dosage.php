<?php
require_once __DIR__ . '/includes/functions.php';
$drugs = get_all_drugs();
$title = 'Check Dosage';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <h1>Daily Drug Dosage</h1>
  <p>When each drug should be taken through the day.</p>
</div>

<div class="dosage-layout">
  <div class="dosage-media">
    <img src="assets/images/pillorganizer.jpg" alt="Weekly pill organiser">
    <p>How drugs are dispensed for daily use: white for morning, purple for afternoon and deep blue for evening.</p>
  </div>

  <div class="table-wrap">
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Drug</th>
            <th>Schedule</th>
            <th>Per day</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$drugs): ?>
            <tr><td colspan="3" class="cell-muted" style="text-align:center;padding:34px">No drugs to show yet.</td></tr>
          <?php else: foreach ($drugs as $drug): ?>
          <tr>
            <td>
              <span class="drug-name"><?= e($drug['name']) ?></span>
              <?php if ($drug['strength']): ?><span class="drug-strength"><?= e($drug['strength']) ?></span><?php endif; ?>
            </td>
            <td><?= schedule_chips_html($drug) ?></td>
            <td><span class="pill-total"><i class="fas fa-tablets"></i> <?= per_day($drug) ?></span></td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
