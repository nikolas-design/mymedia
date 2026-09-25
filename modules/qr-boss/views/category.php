<?php require __DIR__ . '/_tabs.php'; ?>
<a class="back" href="<?= e(module_url('qr-boss', 'menu')) ?>"><?= icon('back', 14) ?> Μενού</a>
<form class="card" method="post" style="max-width:560px">
  <?= csrf_field() ?>
  <label style="margin-top:0">Όνομα κατηγορίας</label><input name="name" value="<?= e($cat['name']) ?>" required>
  <label>Σημείωση (προαιρετικό)</label><input name="note" value="<?= e($cat['note']) ?>" placeholder="π.χ. Σερβίρονται έως τις 12:00">
  <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $cat['active'] ? 'checked' : '' ?>> Εμφανίζεται στο μενού</label>
  <button class="btn dark">Αποθήκευση</button>
</form>
<form method="post" style="max-width:560px" data-confirm="Διαγραφή της κατηγορίας και ΟΛΩΝ των πιάτων της;">
  <?= csrf_field() ?><input type="hidden" name="action" value="delete">
  <button class="btn danger">Διαγραφή κατηγορίας</button>
</form>
