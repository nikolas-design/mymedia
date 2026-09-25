<?= ap_tabs('new') ?>
<?php if (!$services || !$staff): ?>
  <div class="card empty">Πρόσθεσε πρώτα υπηρεσίες και συνεργάτες.</div>
<?php else: ?>
<form method="post" class="cols" id="apform">
  <?= csrf_field() ?>
  <div class="card">
    <label style="margin-top:0">Υπηρεσία</label>
    <select name="service_id" id="svc"><?php foreach ($services as $s): ?><option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?> · <?= (int) $s['duration_min'] ?>'<?= $s['price_cents'] !== null ? ' · ' . money($s['price_cents']) : '' ?></option><?php endforeach; ?></select>
    <label>Συνεργάτης</label>
    <select name="staff_id" id="stf"><?php foreach ($staff as $s): ?><option value="<?= (int) $s['id'] ?>"><?= e($s['name']) ?></option><?php endforeach; ?></select>
    <label>Ημέρα</label><input type="date" name="day" id="day" value="<?= e($preDay) ?>">
    <label>Ώρα</label>
    <div id="slots" class="btns"></div>
    <input type="hidden" name="time" id="time">
  </div>
  <div class="card">
    <label style="margin-top:0">Όνομα πελάτη</label><input name="name" required autocomplete="off">
    <div class="row2">
      <div><label>Τηλέφωνο</label><input name="phone" type="tel" required></div>
      <div><label>Email</label><input name="email" type="email"></div>
    </div>
    <label>Σημειώσεις</label><input name="notes">
    <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="send_email" value="1" checked> Email επιβεβαίωσης στον πελάτη</label>
    <button class="btn dark">Καταχώρηση</button>
  </div>
</form>
<script>
(function () {
  var links = <?= json_encode($links) ?>, url = <?= json_encode(module_url('appointments', 'slots')) ?>;
  var svc = document.getElementById('svc'), stf = document.getElementById('stf'), day = document.getElementById('day'), box = document.getElementById('slots'), time = document.getElementById('time');
  function load() {
    var allowed = links[svc.value] || [];
    Array.prototype.forEach.call(stf.options, function (o) { o.hidden = allowed.indexOf(parseInt(o.value, 10)) < 0; });
    if (stf.selectedOptions[0] && stf.selectedOptions[0].hidden) { var first = Array.prototype.find.call(stf.options, function (o) { return !o.hidden; }); if (first) stf.value = first.value; }
    box.innerHTML = '<span class="small muted">Φόρτωση…</span>'; time.value = '';
    fetch(url + '?service=' + svc.value + '&staff=' + stf.value + '&day=' + day.value, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (s) {
      box.innerHTML = s.length ? '' : '<span class="small muted">Καμία ελεύθερη ώρα.</span>';
      s.forEach(function (t) {
        var b = document.createElement('button'); b.type = 'button'; b.className = 'btn sm'; b.textContent = t;
        b.onclick = function () { box.querySelectorAll('button').forEach(function (x) { x.classList.remove('dark'); }); b.classList.add('dark'); time.value = t; };
        box.appendChild(b);
      });
    });
  }
  [svc, stf, day].forEach(function (el) { el.addEventListener('change', load); });
  document.getElementById('apform').addEventListener('submit', function (e) { if (!time.value) { e.preventDefault(); alert('Διάλεξε ώρα.'); } });
  load();
})();
</script>
<?php endif; ?>
