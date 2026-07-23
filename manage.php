<?php
require_once __DIR__ . '/includes/functions.php';
$drugs = get_all_drugs();
$title = 'Manage drug info';
require __DIR__ . '/includes/header.php';
?>
<div class="section-bar">
  <div class="page-head" style="margin:0">
    <h1>Manage Drugs</h1>
    <p>Add, edit or remove the drugs you are tracking.</p>
  </div>
  <a class="btn btn-primary" href="add-drug.php"><i class="fas fa-plus"></i> New Drug</a>
</div>

<?php if (!$drugs): ?>
  <div class="table-wrap">
    <div class="empty-state">
      <i class="fas fa-pills"></i>
      <p>No drugs yet. Add your first one to get started.</p>
      <a class="btn btn-primary" href="add-drug.php"><i class="fas fa-plus"></i> New Drug</a>
    </div>
  </div>
<?php else: ?>
  <div class="table-wrap">
    <div class="table-scroll">
      <table>
        <thead>
          <tr>
            <th>Drug</th>
            <th>Schedule</th>
            <th>Per day</th>
            <th>Card / Pack</th>
            <th style="text-align:right">Action</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($drugs as $drug): ?>
          <tr>
            <td>
              <span class="drug-name"><?= e($drug['name']) ?></span>
              <?php if ($drug['strength']): ?><span class="drug-strength"><?= e($drug['strength']) ?></span><?php endif; ?>
            </td>
            <td><?= schedule_chips_html($drug) ?></td>
            <td><span class="pill-total"><i class="fas fa-tablets"></i> <?= per_day($drug) ?></span></td>
            <td class="cell-muted">
              <?= $drug['card'] !== null ? e($drug['card']) : '&mdash;' ?> /
              <?= $drug['pack'] !== null ? e($drug['pack']) : '&mdash;' ?>
            </td>
            <td>
              <div class="row-actions" style="justify-content:flex-end">
                <a class="icon-btn" href="update-drug.php?id=<?= e($drug['id']) ?>" title="Edit"><i class="fas fa-pen"></i></a>
                <form action="delete.php" method="POST" class="delete-form" data-name="<?= e($drug['name']) ?>" style="display:inline">
                  <input type="hidden" name="id" value="<?= e($drug['id']) ?>">
                  <button type="submit" class="icon-btn danger" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
<?php endif; ?>
<?php require __DIR__ . '/includes/footer.php'; ?>
