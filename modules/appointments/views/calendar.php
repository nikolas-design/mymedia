<?= ap_tabs('calendar') ?>
<?php if ($setupNeeded && $canEdit): ?>
  <div class="card">
    <div class="cardhd"><?= tile('bolt', 'pink') ?><div><b>Ξεκίνα σε 3 βήματα</b><small>Και οι πελάτες σου κλείνουν ραντεβού online, 24/7.</small></div></div>
    <div class="step"><span>1. Πρόσθεσε υπηρεσίες (π.χ. Κούρεμα 30')</span><a class="btn sm" href="<?= e(module_url('appointments', 'services')) ?>">Υπηρεσίες</a></div>
    <div class="step"><span>2. Πρόσθεσε συνεργάτες με ωράριο</span><a class="btn sm" href="<?= e(module_url('appointments', 'staff')) ?>">Συνεργάτες</a></div>
    <div class="step"><span>3. Μοιράσου τη σελίδα κρατήσεων (Instagram, Google, QR)</span><a class="btn sm" href="<?= e(module_url('appointments', 'settings')) ?>">Σελίδα</a></div>
  </div>
<?php endif; ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px">
  <div class="btns" style="align-items:center">
    <a class="btn sm" href="<?= e(module_url('appointments', '?d=' . date('Y-m-d', strtotime("$day -1 day")))) ?>">‹</a>
    <form method="get"><input type="date" name="d" value="<?= e($day) ?>" onchange="this.form.submit()" style="height:34px;width:auto"></form>
    <a class="btn sm" href="<?= e(module_url('appointments', '?d=' . date('Y-m-d', strtotime("$day +1 day")))) ?>">›</a>
    <?php if ($day !== date('Y-m-d')): ?><a class="btn sm" href="<?= e(module_url('appointments')) ?>">Σήμερα</a><?php endif; ?>
    <b><?= e(AP_DAYS[(int) date('N', strtotime($day))]) ?></b>
  </div>
  <span class="small muted">Εβδομάδα: <b><?= (int) $week['n'] ?></b> ραντεβού · <?= money($week['rev']) ?></span>
</div>

<?php if ($pending): ?>
  <div class="card lst" style="border-color:var(--warn)">
    <?php foreach ($pending as $b): ?>
      <a class="lr" href="<?= e(module_url('appointments', 'bookings/' . $b['id'])) ?>"><span class="grow"><b><?= e($b['customer']) ?> · <?= e($b['service_name']) ?></b><small><?= e(ap_dt($b['starts_at'])) ?></small></span><?= pill('Θέλει επιβεβαίωση', 'orange') ?></a>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<div class="cols" style="grid-template-columns:repeat(auto-fit,minmax(260px,1fr))">
  <?php foreach ($staff as $s): ?>
    <div>
      <p class="sechd" style="margin-top:0"><span class="sw" style="background:<?= e($s['color']) ?>"></span> <?= e($s['name']) ?> (<?= count($byStaff[(int) $s['id']] ?? []) ?>)</p>
      <div class="card lst">
        <?php foreach ($byStaff[(int) $s['id']] ?? [] as $b): ?>
          <a class="lr" href="<?= e(module_url('appointments', 'bookings/' . $b['id'])) ?>" style="border-left:3px solid <?= e($s['color']) ?>;padding-left:10px">
            <span style="font-weight:700;width:52px"><?= e(date('H:i', strtotime($b['starts_at']))) ?></span>
            <span class="grow"><b><?= e($b['customer']) ?></b><small><?= e($b['service_name']) ?> · έως <?= e(date('H:i', strtotime($b['ends_at']))) ?></small></span>
            <?= $b['status'] === 'confirmed' ? '' : pill(...AP_STATUS[$b['status']]) ?>
          </a>
        <?php endforeach; ?>
        <?php if (empty($byStaff[(int) $s['id']])): ?><div class="empty">Ελεύθερη μέρα</div><?php endif; ?>
      </div>
    </div>
  <?php endforeach; ?>
</div>
<a class="btn dark auto mt" href="<?= e(module_url('appointments', 'new?day=' . $day)) ?>"><?= icon('plus', 16) ?> Νέο ραντεβού</a>
