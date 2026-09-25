<?= hb_tabs('bookings') ?>
<a class="back" href="<?= e(module_url('hotel-booking', 'bookings')) ?>"><?= icon('back', 14) ?> Κρατήσεις</a>
<?php if ($isNew): ?>
<form class="card" method="post" style="max-width:720px"><?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'green') ?><div><b>Νέα κράτηση</b><small>Από τηλέφωνο, email ή άλλη πλατφόρμα (για να μην πουληθεί διπλά).</small></div></div>
  <label>Δωμάτιο</label><select name="room_id"><?php foreach ($rooms as $r): ?><option value="<?= (int) $r['id'] ?>"><?= e($r['name']) ?></option><?php endforeach; ?></select>
  <div class="row2"><div><label>Άφιξη</label><input type="date" name="checkin" required></div><div><label>Αναχώρηση</label><input type="date" name="checkout" required></div></div>
  <div class="row2"><div><label>Όνομα</label><input name="name" required></div><div><label>Άτομα</label><input type="number" name="guests" value="2" min="1"></div></div>
  <div class="row2"><div><label>Email</label><input name="email" type="email"></div><div><label>Τηλέφωνο</label><input name="phone"></div></div>
  <div class="row2"><div><label>Σύνολο € (κενό = αυτόματα)</label><input name="total" inputmode="decimal"></div><div><label>Πηγή</label><select name="source"><option value="phone">Τηλέφωνο / email</option><option value="other">Booking, Airbnb κ.λπ.</option></select></div></div>
  <label>Σημειώσεις</label><input name="notes">
  <button class="btn dark">Καταχώρηση</button>
</form>
<?php else: ?>
<div class="cols">
  <div class="card">
    <div class="cardhd"><?= tile('building', 'green') ?><div><b><?= e($b['name']) ?> · <?= e($b['room']) ?></b><small><?= e(date_gr($b['checkin'])) ?> – <?= e(date_gr($b['checkout'])) ?> · <?= hb_nights($b['checkin'], $b['checkout']) ?> βράδια · <?= (int) $b['guests'] ?> άτομα</small></div><span style="margin-left:auto"><?= pill(...HB_STATUS[$b['status']]) ?></span></div>
    <p class="small"><?= $b['email'] ? '<a href="mailto:' . e($b['email']) . '">' . e($b['email']) . '</a> · ' : '' ?><?= $b['phone'] ? '<a href="tel:' . e($b['phone']) . '">' . e($b['phone']) . '</a>' : '' ?><?= $b['country'] ? ' · ' . e($b['country']) : '' ?></p>
    <table class="tbl"><tbody><tr><td>Σύνολο</td><td class="n"><b><?= money($b['total_cents']) ?></b></td></tr><tr><td>Προκαταβολή <?= (int) $settings['deposit_percent'] ?>%</td><td class="n"><?= money((int) round($b['total_cents'] * $settings['deposit_percent'] / 100)) ?></td></tr><tr><td>Πληρώθηκαν</td><td class="n"><?= money($b['paid_cents']) ?></td></tr></tbody></table>
    <form method="post" class="btns mt"><?= csrf_field() ?>
      <?php if ($b['status'] === 'pending'): ?><button class="btn sm dark" name="do" value="confirm">Επιβεβαίωση (email στον πελάτη)</button><?php endif; ?>
      <?php if ($b['status'] === 'confirmed'): ?><button class="btn sm dark" name="do" value="checkin">Check-in</button><?php endif; ?>
      <?php if ($b['status'] === 'checked_in'): ?><button class="btn sm dark" name="do" value="complete">Check-out</button><?php endif; ?>
      <?php if (in_array($b['status'], ['pending', 'confirmed'], true)): ?><button class="btn sm danger" name="do" value="cancel" onclick="return confirm('Ακύρωση; Ο πελάτης θα ενημερωθεί.')">Ακύρωση</button><?php endif; ?>
    </form>
  </div>
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="do" value="paid">
    <label style="margin-top:0">Ποσό που πληρώθηκε (€)</label><input name="paid" value="<?= e(number_format($b['paid_cents'] / 100, 2, ',', '')) ?>" inputmode="decimal">
    <label>Σημειώσεις</label><textarea name="notes" rows="3"><?= e($b['notes']) ?></textarea>
    <button class="btn">Αποθήκευση</button>
  </form>
</div>
<?php endif; ?>
