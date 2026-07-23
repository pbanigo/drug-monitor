<?php
require_once __DIR__ . '/includes/functions.php';
$drugs = get_all_drugs();
$days = isset($_GET['days']) ? max(1, (int) $_GET['days']) : 30;
$title = 'Purchase Drugs';
require __DIR__ . '/includes/header.php';
?>
<div class="page-head">
  <h1>Plan a Purchase</h1>
  <p>Work out how many cards and packs to buy to last a given number of days.</p>
</div>

<form class="inline-form" method="GET" action="purchase.php">
  <label for="days" style="font-weight:600">Buy for</label>
  <input class="input" type="number" id="days" name="days" min="1" value="<?= e($days) ?>">
  <span class="cell-muted">days</span>
  <button class="btn btn-primary" type="submit">Calculate</button>
</form>

<div class="table-wrap">
  <div class="table-scroll">
    <table>
      <thead>
        <tr>
          <th>Drug</th>
          <th>Tablets needed</th>
          <th>Cards to buy</th>
          <th>Packs to buy</th>
        </tr>
      </thead>
      <tbody>
        <?php if (!$drugs): ?>
          <tr><td colspan="4" class="cell-muted" style="text-align:center;padding:34px">No drugs to show yet.</td></tr>
        <?php else: foreach ($drugs as $drug):
          $daily = per_day($drug);
          $pills = $days * $daily;
        ?>
        <tr>
          <td>
            <span class="drug-name"><?= e($drug['name']) ?></span>
            <?php if ($drug['strength']): ?><span class="drug-strength"><?= e($drug['strength']) ?></span><?php endif; ?>
          </td>
          <td><span class="pill-total"><i class="fas fa-tablets"></i> <?= $pills ?></span></td>
          <td>
            <?php if ($drug['card']): ?>
              <strong><?= (int) ceil($pills / $drug['card']) ?></strong>
              <span class="cell-muted">(<?= (int) $drug['card'] ?> per card)</span>
            <?php else: ?>
              <span class="cell-muted" title="Set the card size on the Manage page">&mdash;</span>
            <?php endif; ?>
          </td>
          <td>
            <?php if ($drug['pack']): ?>
              <strong><?= (int) ceil($pills / $drug['pack']) ?></strong>
              <span class="cell-muted">(<?= (int) $drug['pack'] ?> per pack)</span>
            <?php else: ?>
              <span class="cell-muted" title="Set the pack size on the Manage page">&mdash;</span>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<p class="cell-muted" style="margin-top:14px;font-size:.85rem">
  Tip: set each drug's card and pack size on the <a href="manage.php">Manage</a> page to see buy quantities.
</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
