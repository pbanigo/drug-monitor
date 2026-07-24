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
              <?php if ($drug['unit_type'] === 'bottle'): ?>
                <?= $drug['pack'] !== null ? e($drug['pack']) . '/bottle' : '&mdash;' ?>
              <?php else: ?>
                <?= $drug['card'] !== null ? e($drug['card']) : '&mdash;' ?> /
                <?= $drug['pack'] !== null ? e($drug['pack']) : '&mdash;' ?>
              <?php endif; ?>
            </td>
            <td>
              <div class="row-actions" style="justify-content:flex-end">
                <button type="button" class="icon-btn expand-btn" data-id="<?= e($drug['id']) ?>" data-name="<?= e($drug['name']) ?>" title="Details"><i class="fas fa-chevron-down"></i></button>
                <a class="icon-btn" href="update-drug.php?id=<?= e($drug['id']) ?>" title="Edit"><i class="fas fa-pen"></i></a>
                <form action="delete.php" method="POST" class="delete-form" data-name="<?= e($drug['name']) ?>" style="display:inline">
                  <input type="hidden" name="id" value="<?= e($drug['id']) ?>">
                  <button type="submit" class="icon-btn danger" title="Delete"><i class="fas fa-trash"></i></button>
                </form>
              </div>
            </td>
          </tr>
          <tr class="detail-row" id="detail-<?= e($drug['id']) ?>" style="display:none">
            <td colspan="5">
              <div class="detail-grid">
                <div class="detail-photos">
                  <?php if ($drug['pack_photo']): ?>
                    <figure><img src="uploads/<?= e($drug['pack_photo']) ?>" alt="Pack photo"><figcaption>Pack</figcaption></figure>
                  <?php endif; ?>
                  <?php if ($drug['pill_photo']): ?>
                    <figure><img src="uploads/<?= e($drug['pill_photo']) ?>" alt="Pill photo"><figcaption>Pill</figcaption></figure>
                  <?php endif; ?>
                  <?php if (!$drug['pack_photo'] && !$drug['pill_photo']): ?>
                    <span class="cell-muted"><i class="fas fa-image"></i> No photos yet. Add them from <a href="update-drug.php?id=<?= e($drug['id']) ?>">Edit</a>.</span>
                  <?php endif; ?>
                </div>
                <div class="detail-warnings">
                  <h4><i class="fas fa-triangle-exclamation"></i> Label warnings</h4>
                  <div class="warnings-box cell-muted">Expand to check openFDA.</div>
                  <p class="disclaimer">Information from openFDA where available. Not medical advice. Always check with your pharmacist or doctor.</p>
                </div>
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
