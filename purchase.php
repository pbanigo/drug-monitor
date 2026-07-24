<?php
require_once __DIR__ . '/includes/functions.php';
$drugs = get_all_drugs();
$days = isset($_GET['days']) ? max(1, (int) $_GET['days']) : 30;
$remaining_in = isset($_GET['remaining']) && is_array($_GET['remaining']) ? $_GET['remaining'] : [];
$title = 'Purchase Drugs';
require __DIR__ . '/includes/header.php';
?>
<div class="section-bar">
  <div class="page-head" style="margin:0">
    <h1>Plan a Purchase</h1>
    <p>Enter what you already have; we work out what is left to buy for the period.</p>
  </div>
  <div class="row-actions no-print">
    <button type="button" class="btn btn-ghost" id="copyList"><i class="fas fa-copy"></i> Copy</button>
    <button type="button" class="btn btn-ghost" id="shareList"><i class="fas fa-share-nodes"></i> Share</button>
    <button type="button" class="btn btn-ghost" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
  </div>
</div>

<form method="GET" action="purchase.php" id="purchaseForm">
  <div class="inline-form no-print">
    <label for="days" style="font-weight:600">Buy for</label>
    <input class="input" type="number" id="days" name="days" min="1" value="<?= e($days) ?>">
    <span class="cell-muted">days</span>
    <button class="btn btn-primary" type="submit">Calculate</button>
  </div>

  <div class="print-title" style="display:none">Purchase list &middot; <?= e($days) ?> days</div>

  <div class="table-wrap">
    <div class="table-scroll">
      <table id="purchaseTable">
        <thead>
          <tr>
            <th>Drug</th>
            <th>Needed</th>
            <th class="no-print">Remaining</th>
            <th>To buy</th>
          </tr>
        </thead>
        <tbody>
          <?php if (!$drugs): ?>
            <tr><td colspan="4" class="cell-muted" style="text-align:center;padding:34px">No drugs to show yet.</td></tr>
          <?php else: foreach ($drugs as $drug):
            $have = isset($remaining_in[$drug['id']]) ? max(0, (int) $remaining_in[$drug['id']]) : 0;
            $plan = purchase_plan($drug, $days, $have);
          ?>
          <tr data-name="<?= e($drug['name'] . ($drug['strength'] ? ' ' . $drug['strength'] : '')) ?>"
              data-qty="<?= e($plan['share_qty']) ?>"
              data-tobuy="<?= (int) $plan['to_buy'] ?>">
            <td>
              <span class="drug-name"><?= e($drug['name']) ?></span>
              <?php if ($drug['strength']): ?><span class="drug-strength"><?= e($drug['strength']) ?></span><?php endif; ?>
            </td>
            <td><span class="pill-total"><i class="fas fa-tablets"></i> <?= $plan['needed'] ?></span></td>
            <td class="no-print">
              <input class="input" type="number" min="0" style="max-width:100px"
                     name="remaining[<?= e($drug['id']) ?>]" value="<?= $have ?>">
            </td>
            <td>
              <?php if ($plan['to_buy'] === 0): ?>
                <span class="cell-muted">Enough in stock</span>
              <?php elseif ($drug['unit_type'] === 'bottle'): ?>
                <?php if ($plan['bottles'] !== null): ?>
                  <strong><?= $plan['bottles'] ?></strong> <span class="cell-muted">bottle<?= $plan['bottles'] === 1 ? '' : 's' ?> (<?= (int) $drug['pack'] ?>/bottle)</span>
                <?php else: ?>
                  <strong><?= $plan['to_buy'] ?></strong> <span class="cell-muted">tablets (set bottle size)</span>
                <?php endif; ?>
              <?php else: ?>
                <?php if ($plan['cards'] !== null): ?>
                  <strong><?= $plan['cards'] ?></strong> <span class="cell-muted">card<?= $plan['cards'] === 1 ? '' : 's' ?> (<?= (int) $drug['card'] ?>/card)</span>
                <?php elseif ($plan['packs'] !== null): ?>
                  <strong><?= $plan['packs'] ?></strong> <span class="cell-muted">pack<?= $plan['packs'] === 1 ? '' : 's' ?></span>
                <?php else: ?>
                  <strong><?= $plan['to_buy'] ?></strong> <span class="cell-muted">tablets (set card/pack)</span>
                <?php endif; ?>
              <?php endif; ?>
            </td>
          </tr>
          <?php endforeach; endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</form>
<p class="cell-muted no-print" style="margin-top:14px;font-size:.85rem">
  Tip: set each drug's card, pack or bottle size on the <a href="manage.php">Manage</a> page to see buy quantities. Share sends only the drug names and quantities.
</p>
<?php require __DIR__ . '/includes/footer.php'; ?>
