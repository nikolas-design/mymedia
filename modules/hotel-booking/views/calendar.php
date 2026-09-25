<?= hb_tabs('calendar') ?>
<div class="cols">
  <div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('arrow', 'green') ?><div><b>Αφίξεις σήμερα (<?= count($arrivals) ?>)</b></div></div>
    <?php foreach ($arrivals as $b): ?><a class="step mt" href="<?= e(module_url('hotel-booking', 'bookings/' . $b['id'])) ?>" style="color:inherit;text-decoration:none"><span><?= e($b['name']) ?> · <?= e($b['room']) ?></span><?= pill(...HB_STATUS[$b['status']]) ?></a><?php endforeach; ?></div>
  <div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('logout', 'orange') ?><div><b>Αναχωρήσεις σήμερα (<?= count($departures) ?>)</b></div></div>
    <?php foreach ($departures as $b): ?><a class="step mt" href="<?= e(module_url('hotel-booking', 'bookings/' . $b['id'])) ?>" style="color:inherit;text-decoration:none"><span><?= e($b['name']) ?> · <?= e($b['room']) ?></span><?= pill(...HB_STATUS[$b['status']]) ?></a><?php endforeach; ?></div>
</div>
<div style="display:flex;justify-content:space-between;align-items:center;margin:10px 0">
  <div class="btns"><a class="btn sm" href="<?= e(module_url('hotel-booking', '?from=' . date('Y-m-d', strtotime("$from -21 days")))) ?>">‹</a><a class="btn sm" href="<?= e(module_url('hotel-booking')) ?>">Σήμερα</a><a class="btn sm" href="<?= e(module_url('hotel-booking', '?from=' . date('Y-m-d', strtotime("$from +21 days")))) ?>">›</a></div>
  <a class="btn sm dark" href="<?= e(module_url('hotel-booking', 'bookings/new')) ?>"><?= icon('plus', 14) ?> Νέα κράτηση</a>
</div>
<div class="card scrollx" style="padding:8px">
  <table class="tbl" style="min-width:900px"><thead><tr><th>Δωμάτιο</th><?php foreach ($days as $d): ?><th style="text-align:center;padding:6px 2px"><?= e(['', 'Δ', 'Τ', 'Τ', 'Π', 'Π', 'Σ', 'Κ'][(int) date('N', strtotime($d))]) ?><br><?= e(date('d', strtotime($d))) ?></th><?php endforeach; ?></tr></thead>
    <tbody><?php foreach ($rooms as $r): ?><tr><td class="small"><b><?= e($r['name']) ?></b><br><span class="muted"><?= (int) $r['units'] ?> μονάδ.</span></td>
      <?php foreach ($days as $d): $f = $grid[(int) $r['id']][$d]; ?><td style="text-align:center;padding:4px 2px"><span style="display:inline-block;min-width:26px;padding:4px 0;border-radius:6px;font-weight:700;font-size:12px;background:<?= $f === 0 ? '#fbe9ef' : ($f < (int) $r['units'] ? '#fdf3e2' : '#e8f6ee') ?>;color:<?= $f === 0 ? '#c2344d' : ($f < (int) $r['units'] ? '#b8720f' : '#1e8a5a') ?>"><?= $f ?></span></td><?php endforeach; ?></tr><?php endforeach; ?>
    <?php if (!$rooms): ?><tr><td colspan="22" class="empty">Πρόσθεσε πρώτα τύπους δωματίων. <a href="<?= e(module_url('hotel-booking', 'rooms/new')) ?>">Νέο δωμάτιο</a></td></tr><?php endif; ?></tbody></table>
  <p class="small muted" style="margin:8px">Ο αριθμός δείχνει πόσες μονάδες είναι ελεύθερες κάθε βράδυ.</p>
</div>
