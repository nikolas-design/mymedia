<?= rb_tabs('send') ?>
<div class="cols">
  <form class="card" method="post">
    <?= csrf_field() ?>
    <div class="cardhd" style="margin-bottom:0"><?= tile('mail', 'purple') ?><div><b>Ζήτα αξιολόγηση με email</b><small>Ιδανικό μετά από ραντεβού, παράδοση ή κράτηση.</small></div></div>
    <div class="row2">
      <div><label>Όνομα πελάτη</label><input name="name" placeholder="προαιρετικό"></div>
      <div><label>Email</label><input name="email" type="email" required></div>
    </div>
    <?php if (count($locations) > 1): ?>
      <label>Σημείο</label><select name="location_id"><?php foreach ($locations as $l): ?><option value="<?= (int) $l['id'] ?>"><?= e($l['name']) ?></option><?php endforeach; ?></select>
    <?php else: ?><input type="hidden" name="location_id" value="<?= (int) ($locations[0]['id'] ?? 0) ?>"><?php endif; ?>
    <button class="btn dark">Αποστολή</button>
    <p class="hint">Ο πελάτης λαμβάνει ένα σύντομο email με τον σύνδεσμο αξιολόγησης. Δεν στέλνουμε δεύτερο αίτημα στο ίδιο email μέσα σε 30 ημέρες.</p>
  </form>
  <div>
    <p class="sechd" style="margin-top:0">Τελευταία αιτήματα</p>
    <div class="card lst">
      <?php foreach ($history as $h): ?>
        <div class="lr"><span class="grow"><b><?= e($h['name'] ?: $h['email']) ?></b><small><?= e($h['email']) ?> · <?= e(ago($h['created_at'])) ?><?= $h['by_name'] ? ' · ' . e($h['by_name']) : '' ?></small></span></div>
      <?php endforeach; ?>
      <?php if (!$history): ?><div class="empty">Δεν έχει σταλεί ακόμα αίτημα.</div><?php endif; ?>
    </div>
  </div>
</div>
