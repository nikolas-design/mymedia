<?php require __DIR__ . '/_tabs.php'; ?>

<?php if ($canEdit && (!$codeCount || !$itemCount)): ?>
  <div class="card">
    <div class="cardhd"><?= tile('bolt', 'purple') ?><div><b>Ξεκίνα σε 3 βήματα</b><small>Σε 10 λεπτά έχεις ψηφιακό μενού στα τραπέζια σου.</small></div></div>
    <div class="step"><span>1. Βάλε λογότυπο και χρώμα</span><?= $profile['logo'] ? step_pill('done') : '<a class="btn sm" href="' . e(module_url('qr-boss', 'settings')) . '">Εμφάνιση</a>' ?></div>
    <div class="step"><span>2. Πρόσθεσε κατηγορίες και πιάτα</span><?= $itemCount ? step_pill('done') : '<a class="btn sm" href="' . e(module_url('qr-boss', 'menu')) . '">Μενού</a>' ?></div>
    <div class="step"><span>3. Φτιάξε QR για τα τραπέζια και τύπωσέ τα</span><?= $codeCount ? step_pill('done') : '<a class="btn sm" href="' . e(module_url('qr-boss', 'codes')) . '">Κωδικοί QR</a>' ?></div>
  </div>
<?php endif; ?>

<div class="g2 g4">
  <div class="stat"><small>Σαρώσεις σήμερα</small><b><?= (int) $stats['today'] ?></b></div>
  <div class="stat"><small>7 ημέρες</small><b><?= (int) $stats['week'] ?></b></div>
  <div class="stat"><small>30 ημέρες</small><b><?= (int) $stats['month'] ?></b></div>
  <div class="stat"><small>Ενεργά QR</small><b><?= $codeCount ?></b></div>
</div>

<div class="cols mt">
  <div>
    <div class="card">
      <div class="cardhd"><?= tile('chart', 'blue') ?><div><b>Σαρώσεις</b><small>Τελευταίες 14 ημέρες</small></div></div>
      <?= qr_bars($daily) ?>
    </div>
    <p class="sechd">Τα πιο δημοφιλή QR (30 ημέρες)</p>
    <div class="card lst">
      <?php foreach ($top as $c): ?>
        <a class="lr" href="<?= e(module_url('qr-boss', 'codes/' . $c['id'])) ?>">
          <?= tile(QR_TYPES[$c['type']][1], 'purple') ?>
          <span class="grow"><b><?= e($c['name']) ?></b><small><?= e(qr_type_label($c['type'])) ?><?= $c['table_label'] ? ' · ' . e($c['table_label']) : '' ?></small></span>
          <span class="r"><b><?= (int) $c['n'] ?></b><small>σαρώσεις</small></span>
        </a>
      <?php endforeach; ?>
      <?php if (!$top): ?><div class="empty">Δεν υπάρχουν ακόμα QR. <a href="<?= e(module_url('qr-boss', 'codes')) ?>">Φτιάξε το πρώτο</a></div><?php endif; ?>
    </div>
  </div>
  <div>
    <?php if ($pro): ?>
      <div class="card">
        <div class="cardhd"><?= tile('bell', 'pink') ?><div><b>Κλήσεις τραπεζιών</b><small><?= count($calls) ? count($calls) . ' σε αναμονή' : 'Καμία σε αναμονή' ?></small></div></div>
        <?php foreach ($calls as $c): ?>
          <div class="step"><span><?= e($c['table_label']) ?> · <?= $c['kind'] === 'bill' ? 'Λογαριασμός' : 'Σερβιτόρος' ?></span><span class="small muted"><?= e(ago($c['created_at'])) ?></span></div>
        <?php endforeach; ?>
        <a class="btn" href="<?= e(module_url('qr-boss', 'calls')) ?>">Άνοιγμα οθόνης κλήσεων</a>
      </div>
    <?php else: ?>
      <div class="card">
        <div class="cardhd"><?= tile('bell', 'grey') ?><div><b>Κλήση σερβιτόρου</b><small>Διαθέσιμο στο πλάνο Pro</small></div></div>
        <p class="small muted">Ο πελάτης πατάει «Σερβιτόρος» ή «Λογαριασμό» από το μενού και το βλέπετε αμέσως στο κινητό ή στο tablet του μαγαζιού.</p>
        <a class="btn" href="<?= e(url('tools/qr-boss')) ?>">Αναβάθμιση σε Pro</a>
      </div>
    <?php endif; ?>
    <div class="card">
      <div class="cardhd"><?= tile('utensils', 'orange') ?><div><b>Το μενού σου</b><small><?= $itemCount ?> πιάτα και ποτά</small></div></div>
      <div class="btns"><a class="btn" href="<?= e(module_url('qr-boss', 'menu')) ?>">Επεξεργασία</a><a class="btn" href="<?= e(module_url('qr-boss', 'preview')) ?>" target="_blank" rel="noopener">Προβολή</a></div>
    </div>
  </div>
</div>
