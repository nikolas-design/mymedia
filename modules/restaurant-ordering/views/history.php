<?= ro_tabs('history') ?>
<?php $valid = array_filter($orders, fn($o) => in_array($o['status'], ['accepted', 'ready', 'out', 'completed'], true)); ?>
<div class="cols">
<div>
  <form method="get" class="inline" style="margin-bottom:12px"><input type="date" name="d" value="<?= e($day) ?>" onchange="this.form.submit()" style="max-width:200px"><span class="small muted"><?= count($valid) ?> παραγγελίες · <b><?= money(array_sum(array_column($valid, 'total_cents'))) ?></b></span></form>
  <div class="card lst">
    <?php foreach ($orders as $o): ?>
      <a class="lr" href="<?= e(module_url('restaurant-ordering', 'orders/' . $o['id'])) ?>"><span class="grow"><b>#<?= (int) $o['number'] ?> · <?= e($o['name']) ?></b><small><?= e(date('H:i', strtotime($o['created_at']))) ?> · <?= e(RO_KIND[$o['kind']]) ?> · <?= money($o['total_cents']) ?></small></span><?= pill(...RO_STATUS[$o['status']]) ?></a>
    <?php endforeach; ?>
    <?php if (!$orders): ?><div class="empty">Καμία παραγγελία αυτή τη μέρα.</div><?php endif; ?>
  </div>
</div>
<div>
  <div class="card"><div class="cardhd"><?= tile('chart', 'orange') ?><div><b>Τζίρος (€)</b><small>14 ημέρες</small></div></div><?= day_bars($daily) ?></div>
  <div class="card"><div class="cardhd"><?= tile('star', 'orange') ?><div><b>Δημοφιλέστερα</b><small>30 ημέρες</small></div></div>
    <?php $max = max(1, ...array_map(fn($r) => (int) $r['q'], $top ?: [['q' => 1]])); foreach ($top as $r): ?><div class="hb"><span><?= e($r['name']) ?></span><i><b style="width:<?= round($r['q'] / $max * 100) ?>%;background:#c2410c"></b></i><em><?= (int) $r['q'] ?></em></div><?php endforeach; ?>
    <?php if (!$top): ?><div class="empty">Δεν υπάρχουν ακόμα στοιχεία.</div><?php endif; ?></div>
</div>
</div>
