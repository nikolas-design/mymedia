<?php require __DIR__ . '/_tabs.php'; $tags = explode(',', (string) ($item['tags'] ?? '')); ?>
<a class="back" href="<?= e(module_url('qr-boss', 'menu')) ?>"><?= icon('back', 14) ?> Μενού</a>
<form class="card" method="post" enctype="multipart/form-data" style="max-width:640px">
  <?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('utensils', 'orange') ?><div><b><?= $isNew ? 'Νέο πιάτο ή ποτό' : e($item['name']) ?></b></div></div>
  <label>Κατηγορία</label>
  <select name="category_id"><?php foreach ($categories as $c): ?><option value="<?= (int) $c['id'] ?>" <?= (int) $c['id'] === $selectedCat ? 'selected' : '' ?>><?= e($c['name']) ?></option><?php endforeach; ?></select>
  <div class="row2">
    <div><label>Όνομα</label><input name="name" value="<?= e($item['name'] ?? '') ?>" required autofocus></div>
    <div><label>Τιμή (€)</label><input name="price" value="<?= isset($item['price_cents']) ? e(number_format($item['price_cents'] / 100, 2, ',', '')) : '' ?>" inputmode="decimal" placeholder="π.χ. 4,50"></div>
  </div>
  <label>Περιγραφή</label><textarea name="description" rows="2" maxlength="400" placeholder="Υλικά, μέγεθος, αλλεργιογόνα…"><?= e($item['description'] ?? '') ?></textarea>
  <label>Ετικέτες</label>
  <div class="btns">
    <?php foreach (QR_TAGS as $k => $label): ?>
      <label style="display:inline-flex;gap:6px;align-items:center;margin:0;font-weight:400;border:1px solid var(--line);border-radius:999px;padding:6px 10px"><input type="checkbox" name="tags[]" value="<?= $k ?>" <?= in_array($k, $tags, true) ? 'checked' : '' ?>> <?= e($label) ?></label>
    <?php endforeach; ?>
  </div>
  <label>Φωτογραφία</label>
  <?php if (!empty($item['photo'])): ?>
    <div style="display:flex;gap:12px;align-items:center;margin-bottom:8px"><img class="thumb" style="width:96px;height:96px" src="<?= e(upload_url($item['photo'])) ?>" alt="">
      <label style="margin:0;font-weight:400;display:flex;gap:6px;align-items:center"><input type="checkbox" name="remove_photo" value="1"> Αφαίρεση</label></div>
  <?php endif; ?>
  <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" style="height:auto;padding:10px">
  <p class="hint">JPG, PNG ή WebP. Μικραίνει αυτόματα για γρήγορο φόρτωμα.</p>
  <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="available" value="1" <?= ($item['available'] ?? 1) ? 'checked' : '' ?>> Διαθέσιμο</label>
  <div class="btns">
    <button class="btn dark">Αποθήκευση</button>
    <?php if ($isNew): ?><button class="btn" name="again" value="1">Αποθήκευση & επόμενο</button><?php endif; ?>
  </div>
</form>
<?php if (!$isNew): ?>
<form method="post" style="max-width:640px" data-confirm="Διαγραφή του «<?= e($item['name']) ?>»;">
  <?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή</button>
</form>
<?php endif; ?>
