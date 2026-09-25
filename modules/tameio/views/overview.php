<?= tm_tabs('overview') ?>
<?php $todaySales = array_sum(array_map('tm_sales', $todayClosings)); $todayExp = array_sum(array_column($todayExpenses, 'amount_cents')); ?>
<div class="g2 g4">
  <div class="stat dark"><small>Πωλήσεις σήμερα</small><b><?= money($todaySales) ?></b></div>
  <div class="stat"><small>Έξοδα σήμερα</small><b><?= money($todayExp) ?></b></div>
  <?php if ($stats): ?>
    <div class="stat"><small>Εβδομάδα</small><b><?= money($stats['week']) ?></b></div>
    <div class="stat"><small>Μήνας (έξοδα <?= money($stats['month_exp']) ?>)</small><b><?= money($stats['month']) ?></b></div>
  <?php endif; ?>
</div>
<div class="cols mt">
<div>
  <p class="sechd" style="margin-top:0">Κλεισίματα σήμερα</p>
  <div class="card lst">
    <?php foreach ($todayClosings as $c): $diff = $c['counted_cents'] === null ? null : (int) $c['counted_cents'] - tm_expected($c); ?>
      <a class="lr" href="<?= e(module_url('tameio', 'closings/' . $c['id'])) ?>">
        <?= tile('wallet', 'teal') ?>
        <span class="grow"><b><?= e($c['shift']) ?> · <?= money(tm_sales($c)) ?></b><small>Μετρητά <?= money($c['cash_cents']) ?> · Κάρτα <?= money($c['card_cents']) ?><?= $c['by_name'] ? ' · ' . e($c['by_name']) : '' ?></small></span>
        <?= tm_diff_pill($diff) ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$todayClosings): ?><div class="empty">Δεν έχει γίνει ακόμα κλείσιμο σήμερα.</div><?php endif; ?>
  </div>
  <a class="btn dark" href="<?= e(module_url('tameio', 'close')) ?>"><?= icon('wallet', 16) ?> Κλείσιμο ταμείου</a>
  <?php if ($stats): ?>
    <div class="card mt">
      <div class="cardhd"><?= tile('chart', 'blue') ?><div><b>Πωλήσεις (€)</b><small>Τελευταίες 14 ημέρες</small></div></div>
      <?= day_bars($daily) ?>
    </div>
  <?php endif; ?>
</div>
<div>
  <p class="sechd" style="margin-top:0">Έξοδα σήμερα</p>
  <div class="card lst">
    <?php foreach ($todayExpenses as $x): ?>
      <a class="lr" href="<?= e(module_url('tameio', 'expenses/' . $x['id'])) ?>"><span class="grow"><b><?= e($x['category']) ?><?= $x['description'] ? ' · ' . e($x['description']) : '' ?></b><small><?= e(TM_PAYMENT[$x['payment']]) ?><?= $x['photo'] ? ' · 📎 απόδειξη' : '' ?></small></span><b><?= money($x['amount_cents']) ?></b></a>
    <?php endforeach; ?>
    <?php if (!$todayExpenses): ?><div class="empty">Κανένα έξοδο σήμερα.</div><?php endif; ?>
  </div>
  <a class="btn" href="<?= e(module_url('tameio', 'expenses')) ?>#new"><?= icon('plus', 16) ?> Νέο έξοδο</a>
</div>
</div>
