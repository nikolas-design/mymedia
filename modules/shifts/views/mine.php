<?= sh_tabs('mine') ?>
<?php if (!$me): ?>
  <div class="card"><p class="body mb0">Δεν βρέθηκες στο προσωπικό του προγράμματος. Ζήτα από τον υπεύθυνο να σε προσθέσει με το email <b><?= e($user['email']) ?></b>.</p></div>
<?php else: ?>
  <div class="card lst" style="max-width:640px">
    <?php foreach ($shifts as $s): $pub = $settings['published_until'] && $s['day'] <= $settings['published_until']; ?>
      <div class="lr">
        <?= tile('calendar', $s['day'] === date('Y-m-d') ? 'purple' : 'blue') ?>
        <span class="grow"><b><?= e(SH_DAYS[(int) date('N', strtotime($s['day'])) - 1]) ?> <?= e(date_gr($s['day'], false)) ?></b><small><?= e($s['position'] ?? '') ?></small></span>
        <b><?= e(sh_time($s['start_time'])) ?>–<?= e(sh_time($s['end_time'])) ?></b>
        <?= $pub ? '' : pill('Πρόχειρο', 'grey') ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$shifts): ?><div class="empty">Δεν έχεις βάρδιες ακόμα.</div><?php endif; ?>
  </div>
  <a class="btn auto" href="<?= e(module_url('shifts', 'requests')) ?>">Αίτημα για ρεπό, άδεια ή αλλαγή</a>
<?php endif; ?>
