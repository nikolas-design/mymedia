<?= tm_tabs('expenses') ?>
<?php $total = array_sum(array_column($expenses, 'amount_cents')); ?>
<div class="cols">
<div>
  <form method="get" class="inline" style="margin-bottom:12px"><input type="month" name="m" value="<?= e($month) ?>" onchange="this.form.submit()" style="max-width:200px"><span class="small muted">Σύνολο: <b><?= money($total) ?></b></span></form>
  <div class="card lst">
    <?php foreach ($expenses as $x): ?>
      <a class="lr" href="<?= e(module_url('tameio', 'expenses/' . $x['id'])) ?>">
        <?php if ($x['photo']): ?><img class="thumb" src="<?= e(upload_url($x['photo'])) ?>" alt=""><?php else: ?><?= tile('receipt', 'grey', 56) ?><?php endif; ?>
        <span class="grow"><b><?= e($x['category']) ?><?= $x['description'] ? ' · ' . e($x['description']) : '' ?></b><small><?= e(date_gr($x['day'], false)) ?> · <?= e(TM_PAYMENT[$x['payment']]) ?><?= $x['by_name'] ? ' · ' . e($x['by_name']) : '' ?></small></span>
        <b><?= money($x['amount_cents']) ?></b>
      </a>
    <?php endforeach; ?>
    <?php if (!$expenses): ?><div class="empty">Κανένα έξοδο αυτόν τον μήνα.</div><?php endif; ?>
  </div>
</div>
<form class="card" method="post" enctype="multipart/form-data" id="new">
  <?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'teal') ?><div><b>Νέο έξοδο</b></div></div>
  <div class="row2">
    <div><label>Ποσό (€)</label><input name="amount" inputmode="decimal" required placeholder="0,00"></div>
    <div><label>Ημέρα</label><input type="date" name="day" value="<?= date('Y-m-d') ?>" max="<?= date('Y-m-d') ?>"></div>
  </div>
  <div class="row2">
    <div><label>Κατηγορία</label><select name="category"><?php foreach (TM_CATEGORIES as $c): ?><option><?= e($c) ?></option><?php endforeach; ?></select></div>
    <div><label>Πληρωμή</label><select name="payment"><?php foreach (TM_PAYMENT as $k => $v): ?><option value="<?= $k ?>"><?= e($v) ?></option><?php endforeach; ?></select></div>
  </div>
  <label>Περιγραφή</label><input name="description" placeholder="π.χ. Γάλα από ΑΒ">
  <label>Φωτογραφία απόδειξης</label><input type="file" name="photo" accept="image/*" capture="environment" style="height:auto;padding:10px">
  <button class="btn dark">Καταχώρηση</button>
</form>
</div>
