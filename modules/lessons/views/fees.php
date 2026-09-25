<?= ls_tabs('fees') ?>
<?php $amount = array_sum(array_column($rows, 'amount')); $paid = array_sum(array_column($rows, 'paid')); ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px">
  <form method="get"><input type="month" name="m" value="<?= e($month) ?>" onchange="this.form.submit()" style="max-width:200px"></form>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="generate"><input type="hidden" name="m" value="<?= e($month) ?>"><button class="btn sm dark">Χρέωση διδάκτρων <?= e($month) ?></button></form>
</div>
<div class="g2 g4">
  <div class="stat"><small>Χρεώσεις μήνα</small><b><?= money($amount) ?></b></div>
  <div class="stat"><small>Εισπράχθηκαν</small><b><?= money($paid) ?></b></div>
  <div class="stat"><small>Υπόλοιπο μήνα</small><b><?= money($amount - $paid) ?></b></div>
  <div class="stat dark"><small>Συνολικές οφειλές</small><b><?= money($totalOwed) ?></b></div>
</div>
<div class="card lst mt" style="max-width:900px">
  <?php foreach ($rows as $r): $due = (int) $r['amount'] - (int) $r['paid']; ?>
    <a class="lr" href="<?= e(module_url('lessons', 'students/' . $r['id'])) ?>"><span class="grow"><b><?= e($r['name']) ?></b><small><?= e($r['parent_name'] ?? '') ?><?= $r['phone'] ? ' · ' . e($r['phone']) : '' ?></small></span>
      <span class="r"><b><?= money($r['amount']) ?></b><?= $due > 0 ? pill('Οφείλει ' . money($due), 'orange') : pill('Εξοφλήθηκε', 'green') ?></span></a>
  <?php endforeach; ?>
  <?php if (!$rows): ?><div class="empty">Δεν υπάρχουν χρεώσεις για αυτόν τον μήνα. Πάτα «Χρέωση διδάκτρων».</div><?php endif; ?>
</div>
