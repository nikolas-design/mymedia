<?= ap_tabs('customers') ?>
<a class="back" href="<?= e(module_url('appointments', 'customers')) ?>"><?= icon('back', 14) ?> Πελάτες</a>
<div class="cols">
  <form class="card" method="post"><?= csrf_field() ?>
    <label style="margin-top:0">Όνομα</label><input name="name" value="<?= e($c['name']) ?>">
    <div class="row2"><div><label>Τηλέφωνο</label><input value="<?= e($c['phone']) ?>" disabled></div><div><label>Email</label><input name="email" type="email" value="<?= e($c['email']) ?>"></div></div>
    <label>Σημειώσεις (π.χ. προτιμήσεις, αλλεργίες)</label><textarea name="notes" rows="3"><?= e($c['notes']) ?></textarea>
    <div class="btns"><button class="btn dark">Αποθήκευση</button><a class="btn" href="tel:<?= e($c['phone']) ?>">📞 Κλήση</a></div>
  </form>
  <div>
    <p class="sechd" style="margin-top:0">Ιστορικό (<?= count($bookings) ?>)</p>
    <div class="card lst">
      <?php foreach ($bookings as $b): ?>
        <a class="lr" href="<?= e(module_url('appointments', 'bookings/' . $b['id'])) ?>"><span class="grow"><b><?= e($b['service_name']) ?></b><small><?= e(ap_dt($b['starts_at'])) ?> · <?= e($b['staff']) ?></small></span><?= pill(...AP_STATUS[$b['status']]) ?></a>
      <?php endforeach; ?>
    </div>
  </div>
</div>
