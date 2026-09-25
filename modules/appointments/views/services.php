<?= ap_tabs('services') ?>
<div class="cols">
<div>
  <?php foreach ([...$services, null] as $s): ?>
    <form class="card<?= $s && !$s['active'] ? ' off' : '' ?>" method="post">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($s['id'] ?? 0) ?>">
      <?php if (!$s): ?><div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'pink') ?><div><b>Νέα υπηρεσία</b></div></div><?php endif; ?>
      <div class="row2">
        <div><label<?= $s ? ' style="margin-top:0"' : '' ?>>Όνομα</label><input name="name" value="<?= e($s['name'] ?? '') ?>" required placeholder="π.χ. Ανδρικό κούρεμα"></div>
        <div class="row2">
          <div><label<?= $s ? ' style="margin-top:0"' : '' ?>>Λεπτά</label><input name="duration_min" type="number" min="5" step="5" value="<?= (int) ($s['duration_min'] ?? 30) ?>"></div>
          <div><label<?= $s ? ' style="margin-top:0"' : '' ?>>Τιμή €</label><input name="price" inputmode="decimal" value="<?= isset($s['price_cents']) ? e(number_format($s['price_cents'] / 100, 2, ',', '')) : '' ?>"></div>
        </div>
      </div>
      <label>Περιγραφή</label><input name="description" value="<?= e($s['description'] ?? '') ?>">
      <div class="btns mt" style="align-items:center">
        <?php if ($s): ?><label style="margin:0;display:flex;gap:6px;align-items:center"><input type="checkbox" name="active" value="1" <?= $s['active'] ? 'checked' : '' ?>> Ενεργή</label><?php endif; ?>
        <button class="btn sm dark"><?= $s ? 'Αποθήκευση' : 'Προσθήκη' ?></button>
        <?php if ($s): ?><button class="btn sm danger" name="action" value="delete" formnovalidate onclick="return confirm('Διαγραφή υπηρεσίας;')">Διαγραφή</button><?php endif; ?>
      </div>
    </form>
  <?php endforeach; ?>
</div>
<div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('scissors', 'pink') ?><div><b>Συμβουλή</b><small>Η διάρκεια καθορίζει ποιες ώρες βλέπει ο πελάτης. Βάλε λίγα λεπτά παραπάνω για καθάρισμα ή διάλειμμα.</small></div></div></div>
</div>
