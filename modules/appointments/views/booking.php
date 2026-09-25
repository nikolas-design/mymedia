<?= ap_tabs('calendar') ?>
<a class="back" href="<?= e(module_url('appointments', '?d=' . substr($b['starts_at'], 0, 10))) ?>"><?= icon('back', 14) ?> Ημερολόγιο</a>
<div class="cols">
<div>
  <div class="card">
    <div class="cardhd"><?= tile('calendar', 'pink') ?><div><b><?= e($b['service_name']) ?> · <?= e($b['staff']) ?></b><small><?= e(ap_dt($b['starts_at'])) ?> – <?= e(date('H:i', strtotime($b['ends_at']))) ?></small></div><span style="margin-left:auto"><?= pill(...AP_STATUS[$b['status']]) ?></span></div>
    <div class="step"><span><a href="<?= e(module_url('appointments', 'customers/' . $b['customer_id'])) ?>"><b><?= e($b['customer']) ?></b></a><br><span class="small muted"><?= $history ?> ολοκληρωμένα<?= $noshows ? ' · ' . $noshows . ' φορές δεν ήρθε' : '' ?></span></span>
      <span class="btns"><a class="btn sm" href="tel:<?= e($b['phone']) ?>">📞 <?= e($b['phone']) ?></a></span></div>
    <?php if ($b['email']): ?><p class="small muted mb0"><?= e($b['email']) ?></p><?php endif; ?>
    <p class="small muted"><?= $b['source'] === 'online' ? 'Κλείστηκε online' : 'Καταχωρήθηκε από την ομάδα' ?> · <?= e(ago($b['created_at'])) ?><?= $b['price_cents'] !== null ? ' · ' . money($b['price_cents']) : '' ?></p>
    <form method="post" class="btns"><?= csrf_field() ?><input type="hidden" name="notify" value="1">
      <?php if ($b['status'] === 'pending'): ?><button class="btn dark sm" name="do" value="confirm">Επιβεβαίωση</button><?php endif; ?>
      <?php if (in_array($b['status'], ['confirmed', 'pending'], true)): ?>
        <button class="btn sm" name="do" value="done">✓ Ολοκληρώθηκε</button>
        <button class="btn sm" name="do" value="noshow">Δεν ήρθε</button>
        <button class="btn sm danger" name="do" value="cancel" onclick="return confirm('Ακύρωση; Ο πελάτης θα ενημερωθεί με email.')">Ακύρωση</button>
      <?php endif; ?>
    </form>
  </div>
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="do" value="notes">
    <label style="margin-top:0">Σημειώσεις</label><textarea name="notes" rows="2"><?= e($b['notes']) ?></textarea><button class="btn sm">Αποθήκευση</button></form>
</div>
<?php if (in_array($b['status'], ['confirmed', 'pending'], true)): ?>
<form class="card" method="post" id="moveform"><?= csrf_field() ?><input type="hidden" name="do" value="move"><input type="hidden" name="notify" value="1">
  <div class="cardhd" style="margin-bottom:0"><?= tile('swap', 'blue') ?><div><b>Μεταφορά</b><small>Ο πελάτης ενημερώνεται με email.</small></div></div>
  <label>Νέα ημέρα</label><input type="date" name="day" id="mday" value="<?= e(substr($b['starts_at'], 0, 10)) ?>">
  <div id="mslots" class="btns mt"></div><input type="hidden" name="time" id="mtime">
  <button class="btn dark">Μεταφορά</button>
</form>
<script>
(function () {
  var url = <?= json_encode(module_url('appointments', 'slots') . '?service=' . (int) $b['service_id'] . '&staff=' . (int) $b['staff_id'] . '&ignore=' . (int) $b['id']) ?>;
  var d = document.getElementById('mday'), box = document.getElementById('mslots'), t = document.getElementById('mtime');
  function load() { fetch(url + '&day=' + d.value, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (s) {
    box.innerHTML = s.length ? '' : '<span class="small muted">Καμία ελεύθερη ώρα.</span>'; t.value = '';
    s.forEach(function (x) { var b = document.createElement('button'); b.type = 'button'; b.className = 'btn sm'; b.textContent = x;
      b.onclick = function () { box.querySelectorAll('button').forEach(function (y) { y.classList.remove('dark'); }); b.classList.add('dark'); t.value = x; }; box.appendChild(b); });
  }); }
  d.addEventListener('change', load); load();
})();
</script>
<?php endif; ?>
</div>
