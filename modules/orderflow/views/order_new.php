<?= of_tabs('overview') ?>
<a class="back" href="<?= e(module_url('orderflow')) ?>"><?= icon('back', 14) ?> Προμηθευτές</a>
<h1 style="font-size:24px;margin-top:4px">Παραγγελία σε <?= e($sup['name']) ?></h1>
<form method="post" class="cols">
  <?= csrf_field() ?>
  <div class="card lst">
    <?php foreach ($products as $p): $v = $prefill[(int) $p['id']] ?? 0; ?>
      <div class="lr">
        <span class="grow"><b><?= e($p['name']) ?></b><small><?= e($p['unit']) ?><?= $p['price_cents'] !== null ? ' · ' . money($p['price_cents']) : '' ?><?= isset($lastQty[$p['id']]) ? ' · την προηγούμενη φορά ' . e(of_qty($lastQty[$p['id']])) : '' ?></small></span>
        <div class="inline" style="gap:4px;align-items:center">
          <button class="btn sm" type="button" data-step="-1" style="width:36px;padding:0">−</button>
          <input name="qty[<?= (int) $p['id'] ?>]" value="<?= $v ? e(of_qty($v)) : '' ?>" inputmode="decimal" placeholder="0" style="width:64px;height:36px;text-align:center;padding:0 4px">
          <button class="btn sm" type="button" data-step="1" style="width:36px;padding:0">+</button>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (!$products): ?><div class="empty">Ο προμηθευτής δεν έχει είδη ακόμα. <?php if ($canEdit): ?><a href="<?= e(module_url('orderflow', 'suppliers/' . $sup['id'])) ?>">Πρόσθεσε είδη</a><?php endif; ?></div><?php endif; ?>
    <div class="lr" style="display:block">
      <b class="small muted">Κάτι εκτός καταλόγου</b>
      <div class="inline" style="margin-top:6px"><input name="extra_name" placeholder="Είδος"><input name="extra_qty" placeholder="Ποσ." inputmode="decimal" style="width:80px"><input name="extra_unit" placeholder="μονάδα" style="width:90px"></div>
    </div>
  </div>
  <div class="card">
    <label style="margin-top:0">Παράδοση</label><input type="date" name="delivery_date" value="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>">
    <label>Σημείωση</label><textarea name="note" rows="3" placeholder="π.χ. Παράδοση πριν τις 10:00"></textarea>
    <button class="btn dark">Συνέχεια</button>
    <p class="hint">Στο επόμενο βήμα βλέπεις το κείμενο και το στέλνεις με email, Viber ή WhatsApp.</p>
  </div>
</form>
<script>
document.addEventListener('click', function (e) {
  var b = e.target.closest('[data-step]'); if (!b) return;
  var inp = b.parentNode.querySelector('input'), v = parseFloat((inp.value || '0').replace(',', '.')) || 0;
  v = Math.max(0, v + parseInt(b.dataset.step, 10)); inp.value = v ? String(v).replace('.', ',') : '';
});
</script>
