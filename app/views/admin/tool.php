<a class="back" href="<?= e(url('admin/tools')) ?>"><?= icon('back', 14) ?> Εργαλεία</a>
<h1><?= e($tool['name']) ?></h1>
<div class="cols">
<form class="card" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="tool">
  <label>Όνομα</label><input name="name" value="<?= e($tool['name']) ?>" required>
  <label>Σύντομη περιγραφή (κάρτα)</label><input name="short" value="<?= e($tool['short']) ?>">
  <label>Υπότιτλος</label><input name="tagline" value="<?= e($tool['tagline']) ?>">
  <label>Περιγραφή</label><textarea name="description" rows="3"><?= e($tool['description']) ?></textarea>
  <label>Σχεδιασμένο για</label><input name="audience" value="<?= e($tool['audience']) ?>">
  <label>Χαρακτηριστικά (ένα ανά γραμμή)</label><textarea name="features" rows="4"><?= e($tool['features']) ?></textarea>
  <div class="row2">
    <div><label>Κατάσταση</label><select name="status">
      <?php foreach (['available' => 'Διαθέσιμο', 'soon' => 'Σύντομα', 'hidden' => 'Κρυφό'] as $k => $v): ?><option value="<?= $k ?>" <?= $tool['status'] === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?>
    </select></div>
    <div><label>Σειρά</label><input name="sort" type="number" value="<?= (int) $tool['sort'] ?>"></div>
  </div>
  <div class="row2">
    <div><label>Χρώμα</label><select name="color">
      <?php foreach (['purple','teal','orange','indigo','blue','pink','green','grey'] as $c): ?><option <?= $tool['color'] === $c ? 'selected' : '' ?>><?= $c ?></option><?php endforeach; ?>
    </select></div>
    <div><label>Εικονίδιο</label><select name="icon">
      <?php foreach (array_keys(ICONS) as $i): ?><option <?= $tool['icon'] === $i ? 'selected' : '' ?>><?= $i ?></option><?php endforeach; ?>
    </select></div>
  </div>
  <p class="hint">Διεύθυνση εργαλείου: /t/<?= e($tool['slug']) ?> · Module: <?= module_exists($tool['slug']) ? 'υπάρχει' : 'δεν υπάρχει ακόμα (modules/' . e($tool['slug']) . '/)' ?></p>
  <button class="btn dark">Αποθήκευση</button>
</form>

<div>
  <p class="sechd" style="margin-top:0">Πλάνα (τιμές χωρίς ΦΠΑ)</p>
  <?php foreach ([...$plans, null] as $p): ?>
    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="plan"><input type="hidden" name="id" value="<?= (int) ($p['id'] ?? 0) ?>">
      <?php if (!$p): ?><b>Νέο πλάνο</b><?php endif; ?>
      <div class="row2">
        <div><label>Όνομα</label><input name="name" value="<?= e($p['name'] ?? '') ?>" required></div>
        <div><label>Τιμή</label><input name="price" value="<?= $p ? e(number_format($p['price_cents'] / 100, 2, ',', '')) : '' ?>" inputmode="decimal" required></div>
      </div>
      <label>Περιγραφή</label><input name="summary" value="<?= e($p['summary'] ?? '') ?>">
      <div class="row2">
        <div><label>Περίοδος</label><select name="period"><option value="month">Μήνας</option><option value="year" <?= ($p['period'] ?? '') === 'year' ? 'selected' : '' ?>>Έτος</option></select></div>
        <div><label>Σειρά</label><input name="sort" type="number" value="<?= (int) ($p['sort'] ?? 0) ?>"></div>
      </div>
      <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="popular" value="1" <?= !empty($p['popular']) ? 'checked' : '' ?>> Δημοφιλές</label>
      <label style="display:flex;gap:8px;align-items:center;margin-top:4px"><input type="checkbox" name="active" value="1" <?= !$p || $p['active'] ? 'checked' : '' ?>> Ενεργό</label>
      <button class="btn"><?= $p ? 'Αποθήκευση' : 'Προσθήκη' ?></button>
    </form>
  <?php endforeach; ?>
</div>
</div>
