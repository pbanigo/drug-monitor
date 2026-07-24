<?php
// Shared add/edit form. Expects: $v (values), $error, $submit_label, $form_action, $drug_id.
$is_bottle = ($v['unit_type'] ?? 'blister') === 'bottle';
?>
<a class="back-link" href="manage.php"><i class="fas fa-arrow-left"></i> All drugs</a>

<div class="page-head">
  <h1><?= $drug_id === '' ? 'Add a Drug' : 'Edit ' . e($v['name']) ?></h1>
  <p>Set the name, strength and how many tablets are taken at each time of day.</p>
</div>

<?php if (!empty($error)): ?>
  <div class="alert alert-danger"><?= e($error) ?></div>
<?php endif; ?>

<form class="form-card" action="<?= e($form_action) ?>" method="POST" enctype="multipart/form-data">
  <?php if ($drug_id !== ''): ?>
    <input type="hidden" name="id" value="<?= e($drug_id) ?>">
  <?php endif; ?>

  <div class="scan-panel" id="scanPanel" style="display:none">
    <div class="scan-row">
      <button type="button" class="btn btn-ghost" id="scanStart"><i class="fas fa-barcode"></i> Scan barcode</button>
      <span class="cell-muted" id="scanStatus">Point your camera at the pack barcode to auto-fill the name.</span>
    </div>
    <video id="scanVideo" playsinline muted></video>
  </div>

  <div class="field-row">
    <div class="field">
      <label for="name">Drug name</label>
      <input class="input" type="text" id="name" name="name" value="<?= e($v['name']) ?>" placeholder="e.g. Metformin" required>
    </div>
    <div class="field">
      <label for="strength">Strength <span class="hint">(optional)</span></label>
      <input class="input" type="text" id="strength" name="strength" value="<?= e($v['strength']) ?>" placeholder="e.g. 500mg, 50/500, 5000iu">
    </div>
  </div>

  <div class="field">
    <label>Tablets per time of day</label>
    <div class="dose-grid">
      <div class="dose-cell field" style="margin:0">
        <label for="morning"><span class="dot morning"></span> Morning</label>
        <input class="input" type="number" id="morning" name="morning" min="0" max="50" value="<?= e($v['morning']) ?>">
      </div>
      <div class="dose-cell field" style="margin:0">
        <label for="afternoon"><span class="dot afternoon"></span> Afternoon</label>
        <input class="input" type="number" id="afternoon" name="afternoon" min="0" max="50" value="<?= e($v['afternoon']) ?>">
      </div>
      <div class="dose-cell field" style="margin:0">
        <label for="evening"><span class="dot evening"></span> Evening</label>
        <input class="input" type="number" id="evening" name="evening" min="0" max="50" value="<?= e($v['evening']) ?>">
      </div>
      <div class="dose-cell field" style="margin:0">
        <label for="night"><span class="dot night"></span> Night</label>
        <input class="input" type="number" id="night" name="night" min="0" max="50" value="<?= e($v['night']) ?>">
      </div>
    </div>
  </div>

  <div class="field">
    <label for="unit_type">Packaging</label>
    <select class="input" id="unit_type" name="unit_type">
      <option value="blister" <?= $is_bottle ? '' : 'selected' ?>>Blister pack (cards)</option>
      <option value="bottle"  <?= $is_bottle ? 'selected' : '' ?>>Bottle (loose tablets)</option>
    </select>
  </div>

  <!-- Blister fields -->
  <div class="field-row js-blister-fields" <?= $is_bottle ? 'style="display:none"' : '' ?>>
    <div class="field">
      <label for="card">Tablets per card <span class="hint">(optional)</span></label>
      <input class="input" type="number" id="card" name="card" min="1" max="1000" value="<?= e($v['card']) ?>" placeholder="e.g. 10">
    </div>
    <div class="field">
      <label for="pack">Tablets per pack <span class="hint">(optional)</span></label>
      <input class="input" type="number" id="pack" name="pack" min="1" max="1000" value="<?= e($v['pack']) ?>" placeholder="e.g. 30">
    </div>
  </div>

  <!-- Bottle field (reuses the pack column as bottle size) -->
  <div class="field js-bottle-fields" <?= $is_bottle ? '' : 'style="display:none"' ?>>
    <label for="bottle_size">Tablets per bottle <span class="hint">(optional)</span></label>
    <input class="input" type="number" id="bottle_size" name="bottle_size" min="1" max="5000" value="<?= e($v['pack']) ?>" placeholder="e.g. 100">
  </div>

  <div class="field-row">
    <div class="field">
      <label for="pack_photo">Pack photo <span class="hint">(optional)</span></label>
      <?php if (!empty($v['pack_photo'])): ?>
        <img src="uploads/<?= e($v['pack_photo']) ?>" alt="Current pack photo" class="photo-thumb">
      <?php endif; ?>
      <input class="input" type="file" id="pack_photo" name="pack_photo" accept="image/*">
    </div>
    <div class="field">
      <label for="pill_photo">Pill photo <span class="hint">(optional)</span></label>
      <?php if (!empty($v['pill_photo'])): ?>
        <img src="uploads/<?= e($v['pill_photo']) ?>" alt="Current pill photo" class="photo-thumb">
      <?php endif; ?>
      <input class="input" type="file" id="pill_photo" name="pill_photo" accept="image/*">
    </div>
  </div>

  <div class="form-actions">
    <button class="btn btn-primary" type="submit"><i class="fas fa-check"></i> <?= e($submit_label) ?></button>
    <a class="btn btn-ghost" href="manage.php">Cancel</a>
  </div>
</form>
<script src="assets/js/scan.js"></script>
