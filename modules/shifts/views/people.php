<?= sh_tabs('people') ?>
<div class="cols">
<div>
  <?php foreach ($people as $p): ?>
    <details class="card<?= $p['active'] ? '' : ' off' ?>" style="padding:12px 16px">
      <summary style="display:flex;align-items:center;gap:12px;cursor:pointer;list-style:none">
        <span class="av" style="<?= avatar_style($p['name']) ?>"><?= e(initials($p['name'])) ?></span>
        <span class="grow" style="flex:1"><b><?= e($p['name']) ?></b><br><span class="small muted"><?= e($p['position'] ?? '') ?><?= $p['user_id'] ? ' · έχει λογαριασμό' : '' ?></span></span><?= icon('chevron') ?>
      </summary>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save"><input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
        <div class="row2"><div><label>Όνομα</label><input name="name" value="<?= e($p['name']) ?>" required></div><div><label>Θέση</label><input name="position" value="<?= e($p['position']) ?>"></div></div>
        <div class="row2"><div><label>Κινητό</label><input name="phone" value="<?= e($p['phone']) ?>"></div><div><label>Email</label><input name="email" type="email" value="<?= e($p['email']) ?>"></div></div>
        <label>Ώρες εβδομάδας (σύμβαση)</label><input name="weekly_hours" value="<?= e($p['weekly_hours']) ?>" inputmode="decimal" style="max-width:140px">
        <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $p['active'] ? 'checked' : '' ?>> Ενεργό</label>
        <button class="btn sm dark mt">Αποθήκευση</button>
      </form>
    </details>
  <?php endforeach; ?>
  <?php if (!$people): ?><div class="card empty">Κανένα άτομο ακόμα.</div><?php endif; ?>
</div>
<div>
  <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="import"><button class="btn"><?= icon('users', 16) ?> Εισαγωγή από την ομάδα</button></form>
  <form class="card mt" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="save">
    <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'blue') ?><div><b>Νέο άτομο</b><small>Και χωρίς λογαριασμό στην πύλη. Έως <?= $limit ?> άτομα.</small></div></div>
    <div class="row2"><div><label>Όνομα</label><input name="name" required></div><div><label>Θέση</label><input name="position" placeholder="π.χ. Σερβιτόρος"></div></div>
    <div class="row2"><div><label>Κινητό</label><input name="phone"></div><div><label>Email</label><input name="email" type="email" placeholder="για να λαμβάνει τις βάρδιες"></div></div>
    <label>Ώρες εβδομάδας</label><input name="weekly_hours" inputmode="decimal" style="max-width:140px">
    <button class="btn dark">Προσθήκη</button>
  </form>
</div>
</div>
