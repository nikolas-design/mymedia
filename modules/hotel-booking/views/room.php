<?= hb_tabs('rooms') ?>
<a class="back" href="<?= e(module_url('hotel-booking', 'rooms')) ?>"><?= icon('back', 14) ?> Δωμάτια</a>
<div class="cols">
<form class="card" method="post" enctype="multipart/form-data"><?= csrf_field() ?><input type="hidden" name="action" value="room">
  <label style="margin-top:0">Όνομα</label><input name="name" value="<?= e($r['name'] ?? '') ?>" required placeholder="π.χ. Δίκλινο με θέα θάλασσα">
  <label>Περιγραφή</label><textarea name="description" rows="3"><?= e($r['description'] ?? '') ?></textarea>
  <div class="row2"><div><label>Μέγ. άτομα</label><input type="number" name="capacity" value="<?= (int) ($r['capacity'] ?? 2) ?>" min="1"></div><div><label>Πόσα ίδια δωμάτια</label><input type="number" name="units" value="<?= (int) ($r['units'] ?? 1) ?>" min="1"></div></div>
  <label>Βασική τιμή / βράδυ (€)</label><input name="price" value="<?= isset($r['base_price_cents']) ? e(number_format($r['base_price_cents'] / 100, 2, ',', '')) : '' ?>" inputmode="decimal" required>
  <label>Φωτογραφία</label><?php if (!empty($r['photo'])): ?><img src="<?= e(upload_url($r['photo'])) ?>" alt="" style="width:100%;border-radius:10px;margin-bottom:8px"><?php endif; ?>
  <input type="file" name="photo" accept="image/*" style="height:auto;padding:10px">
  <?php if ($r): ?><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $r['active'] ? 'checked' : '' ?>> Διαθέσιμο για κρατήσεις</label><?php endif; ?>
  <button class="btn dark"><?= $isNew ? 'Προσθήκη' : 'Αποθήκευση' ?></button>
</form>
<?php if ($r): ?>
<div>
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="rate">
    <div class="cardhd" style="margin-bottom:0"><?= tile('chart', 'green') ?><div><b>Τιμές περιόδου</b><small>π.χ. Αύγουστος ακριβότερα.</small></div></div>
    <?php foreach ($rates as $x): ?><div class="step mt"><span><?= e(date_gr($x['date_from'], false)) ?> – <?= e(date_gr($x['date_to'], false)) ?><?= $x['label'] ? ' · ' . e($x['label']) : '' ?> · <b><?= money($x['price_cents']) ?></b></span><button class="btn sm danger" name="action" value="del_rate" formnovalidate onclick="this.form.id.value=<?= (int) $x['id'] ?>">✕</button></div><?php endforeach; ?>
    <input type="hidden" name="id">
    <div class="row2"><div><label>Από</label><input type="date" name="date_from"></div><div><label>Έως</label><input type="date" name="date_to"></div></div>
    <div class="row2"><div><label>Τιμή €</label><input name="price" inputmode="decimal"></div><div><label>Ετικέτα</label><input name="label" placeholder="Υψηλή σεζόν"></div></div>
    <button class="btn">Προσθήκη τιμής</button>
  </form>
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="block">
    <div class="cardhd" style="margin-bottom:0"><?= tile('x', 'pink') ?><div><b>Κλείσιμο ημερών</b><small>Για κρατήσεις από Booking/Airbnb ή συντήρηση.</small></div></div>
    <?php foreach ($blocks as $x): ?><div class="step mt"><span><?= e(date_gr($x['date_from'], false)) ?> – <?= e(date_gr($x['date_to'], false)) ?> · <?= (int) $x['units'] ?> μον.<?= $x['reason'] ? ' · ' . e($x['reason']) : '' ?></span><button class="btn sm danger" name="action" value="del_block" formnovalidate onclick="this.form.id.value=<?= (int) $x['id'] ?>">✕</button></div><?php endforeach; ?>
    <input type="hidden" name="id">
    <div class="row2"><div><label>Από</label><input type="date" name="date_from"></div><div><label>Έως (τελευταίο βράδυ)</label><input type="date" name="date_to"></div></div>
    <div class="row2"><div><label>Μονάδες</label><input type="number" name="units" value="1" min="1"></div><div><label>Λόγος</label><input name="reason"></div></div>
    <button class="btn">Κλείσιμο</button>
  </form>
</div>
<?php endif; ?>
</div>
