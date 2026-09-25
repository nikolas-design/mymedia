<?= of_tabs('orders') ?>
<div class="cols">
<div>
  <form method="get" class="inline" style="margin-bottom:12px">
    <select name="supplier" onchange="this.form.submit()"><option value="">Όλοι οι προμηθευτές</option>
      <?php foreach ($suppliers as $s): ?><option value="<?= (int) $s['id'] ?>" <?= $sup === (int) $s['id'] ? 'selected' : '' ?>><?= e($s['name']) ?></option><?php endforeach; ?></select>
  </form>
  <div class="card lst">
    <?php foreach ($orders as $o): ?>
      <a class="lr" href="<?= e(module_url('orderflow', 'orders/' . $o['id'])) ?>">
        <span class="grow"><b><?= e($o['supplier']) ?> · #<?= (int) $o['id'] ?></b><small><?= e(date_gr($o['created_at'])) ?> · <?= (int) $o['n'] ?> είδη<?= $o['total_cents'] ? ' · ' . money($o['total_cents']) : '' ?></small></span>
        <?= pill(...OF_STATUS[$o['status']]) ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$orders): ?><div class="empty">Καμία παραγγελία ακόμα.</div><?php endif; ?>
  </div>
</div>
<div class="card">
  <div class="cardhd"><?= tile('chart', 'blue') ?><div><b>Αγορές ανά προμηθευτή</b><small>30 ημέρες · εκτίμηση από τις τιμές καταλόγου</small></div></div>
  <?php $max = max(1, ...array_map(fn($r) => (int) $r['total'], $bySupplier ?: [['total' => 1]])); foreach ($bySupplier as $r): ?>
    <div class="hb"><span><?= e($r['name']) ?></span><i><b style="width:<?= round($r['total'] / $max * 100) ?>%"></b></i><em><?= money($r['total']) ?></em></div>
  <?php endforeach; ?>
  <?php if (!$bySupplier): ?><div class="empty">Δεν υπάρχουν ακόμα στοιχεία.</div><?php endif; ?>
</div>
</div>
