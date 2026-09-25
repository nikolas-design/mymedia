<?= ap_tabs('staff') ?>
<div class="cols">
  <div class="card lst">
    <?php foreach ($staff as $s): ?>
      <a class="lr<?= $s['active'] ? '' : ' off' ?>" href="<?= e(module_url('appointments', 'staff/' . $s['id'])) ?>">
        <span class="av" style="background:<?= e($s['color']) ?>;color:#fff"><?= e(initials($s['name'])) ?></span>
        <span class="grow"><b><?= e($s['name']) ?></b><small><?= $s['hours'] ? 'Έχει ωράριο' : 'Χωρίς ωράριο: δεν εμφανίζεται για κρατήσεις' ?></small></span><?= icon('chevron') ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$staff): ?><div class="empty">Κανένας συνεργάτης ακόμα.</div><?php endif; ?>
  </div>
  <div><a class="btn dark" href="<?= e(module_url('appointments', 'staff/new')) ?>"><?= icon('plus', 16) ?> Νέος συνεργάτης</a></div>
</div>
