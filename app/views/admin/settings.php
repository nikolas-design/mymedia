<h1>Ρυθμίσεις</h1>
<div class="cols">
<form class="card" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="settings">
  <div class="cardhd" style="margin-bottom:0"><?= tile('building', 'blue') ?><div><b>Στοιχεία πληρωμής</b><small>Εμφανίζονται στους πελάτες και στα παραστατικά.</small></div></div>
  <?php foreach ($keys as $k => $label): ?>
    <label for="<?= $k ?>"><?= e($label) ?></label>
    <input id="<?= $k ?>" name="<?= $k ?>" value="<?= e(setting($k)) ?>">
  <?php endforeach; ?>
  <button class="btn dark">Αποθήκευση</button>
</form>

<form class="card" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="migrate">
  <div class="cardhd" style="margin-bottom:0"><?= tile('settings', 'purple') ?><div><b>Ενημέρωση βάσης</b><small>Μετά από κάθε ανέβασμα νέας έκδοσης.</small></div></div>
  <?php if ($pending): ?>
    <p class="body mt">Εκκρεμούν <?= count($pending) ?> ενημερώσεις:</p>
    <ul class="small"><?php foreach ($pending as $p): ?><li class="mono"><?= e($p) ?></li><?php endforeach; ?></ul>
    <button class="btn primary">Εφαρμογή ενημερώσεων</button>
  <?php else: ?>
    <p class="body mt mb0">Η βάση είναι ενημερωμένη. <?= pill('OK', 'green') ?></p>
  <?php endif; ?>
</form>
</div>
