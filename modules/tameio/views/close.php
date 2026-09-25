<?= tm_tabs('close') ?>
<form method="post" class="cols" id="closeform">
  <?= csrf_field() ?>
  <div class="card">
    <div class="row2">
      <div><label style="margin-top:0">Ημέρα</label><input type="date" name="day" value="<?= e($day) ?>" max="<?= date('Y-m-d') ?>"></div>
      <div><label style="margin-top:0">Βάρδια</label><input name="shift" value="<?= e($shifts[0]) ?>" list="shifts"><datalist id="shifts"><?php foreach (array_unique([...$shifts, 'Πρωί', 'Απόγευμα', 'Βράδυ']) as $s): ?><option value="<?= e($s) ?>"><?php endforeach; ?></datalist></div>
    </div>
    <label>Ρέστα έναρξης (μετρητά στο συρτάρι το πρωί)</label><input name="opening" inputmode="decimal" value="<?= $lastCounted !== null ? e(number_format($lastCounted / 100, 2, ',', '')) : '' ?>" placeholder="0,00" data-sum>
    <p class="sechd">Πωλήσεις βάρδιας (από την ταμειακή / Z)</p>
    <div class="row2">
      <div><label style="margin-top:0">Μετρητά</label><input name="cash" inputmode="decimal" placeholder="0,00" required data-sum></div>
      <div><label style="margin-top:0">Κάρτα / POS</label><input name="card" inputmode="decimal" placeholder="0,00" data-sum></div>
    </div>
    <label>Άλλο (π.χ. delivery πλατφόρμες)</label><input name="other" inputmode="decimal" placeholder="0,00" data-sum>
  </div>
  <div>
    <div class="card">
      <div class="step"><span>Έξοδα με μετρητά σήμερα</span><b id="cashexp" data-v="<?= $cashExp ?>"><?= money($cashExp) ?></b></div>
      <div class="step"><span>Πρέπει να υπάρχουν στο συρτάρι</span><b id="expected">—</b></div>
      <label>Μετρήθηκαν στο συρτάρι</label><input name="counted" inputmode="decimal" placeholder="0,00" data-sum>
      <div class="step mt"><span>Διαφορά</span><b id="diff">—</b></div>
      <div class="step"><span>Σύνολο πωλήσεων</span><b id="total">—</b></div>
      <label>Σημειώσεις</label><textarea name="notes" rows="2" placeholder="π.χ. επιστροφή σε πελάτη 5€"></textarea>
      <button class="btn dark">Κλείσιμο ταμείου</button>
    </div>
  </div>
</form>
<script>
(function () {
  var f = document.getElementById('closeform');
  function v(n) { var x = (f.elements[n].value || '').replace(/\./g, '').replace(',', '.'); return Math.round((parseFloat(x) || 0) * 100); }
  function m(c) { return (c < 0 ? '−' : '') + '€' + (Math.abs(c) / 100).toLocaleString('el-GR', { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
  function calc() {
    var exp = parseInt(document.getElementById('cashexp').dataset.v, 10);
    var expected = v('opening') + v('cash') - exp;
    document.getElementById('expected').textContent = m(expected);
    document.getElementById('total').textContent = m(v('cash') + v('card') + v('other'));
    var d = document.getElementById('diff');
    if (f.elements.counted.value === '') { d.textContent = '—'; d.style.color = ''; return; }
    var diff = v('counted') - expected;
    d.textContent = Math.abs(diff) < 50 ? 'Σωστό ✓' : (diff > 0 ? 'Περίσσευμα ' : 'Έλλειμμα ') + m(Math.abs(diff));
    d.style.color = Math.abs(diff) < 50 ? '#1e8a5a' : '#c2344d';
  }
  f.addEventListener('input', calc); calc();
})();
</script>
