<?= hb_tabs('bookings') ?>
<div class="btns" style="margin-bottom:12px">
  <a class="btn sm<?= !$status ? ' dark' : '' ?>" href="<?= e(module_url('hotel-booking', 'bookings')) ?>">Επερχόμενες</a>
  <?php foreach (HB_STATUS as $k => [$l]): ?><a class="btn sm<?= $status === $k ? ' dark' : '' ?>" href="<?= e(module_url('hotel-booking', 'bookings?status=' . $k)) ?>"><?= e($l) ?></a><?php endforeach; ?>
  <a class="btn sm" href="<?= e(module_url('hotel-booking', 'bookings/new')) ?>"><?= icon('plus', 14) ?> Νέα</a>
</div>
<div class="card lst" style="max-width:900px">
  <?php foreach ($bookings as $b): ?>
    <a class="lr" href="<?= e(module_url('hotel-booking', 'bookings/' . $b['id'])) ?>"><?= tile('building', 'green') ?>
      <span class="grow"><b><?= e($b['name']) ?> · <?= e($b['room']) ?></b><small><?= e(date_gr($b['checkin'], false)) ?> – <?= e(date_gr($b['checkout'])) ?> · <?= hb_nights($b['checkin'], $b['checkout']) ?> βράδια · <?= (int) $b['guests'] ?> άτ. · <?= money($b['total_cents']) ?></small></span>
      <?= pill(...HB_STATUS[$b['status']]) ?></a>
  <?php endforeach; ?>
  <?php if (!$bookings): ?><div class="empty">Καμία κράτηση.</div><?php endif; ?>
</div>
