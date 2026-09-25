<?php require __DIR__ . '/_tabs.php'; ?>
<div class="cols">
<div>
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <p class="sechd" style="margin:0">Σε αναμονή (<?= count($open) ?>)</p>
    <?php if ($open): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="all"><button class="btn sm">Όλες εξυπηρετήθηκαν</button></form><?php endif; ?>
  </div>
  <?php foreach ($open as $c): ?>
    <div class="card" style="display:flex;align-items:center;gap:12px;border-color:var(--p);box-shadow:0 0 0 1px var(--p)">
      <?= tile($c['kind'] === 'bill' ? 'receipt' : 'bell', $c['kind'] === 'bill' ? 'green' : 'pink', 48) ?>
      <div class="grow" style="flex:1"><b style="font-size:18px"><?= e($c['table_label']) ?></b>
        <div class="small muted"><?= $c['kind'] === 'bill' ? 'Ζητάει λογαριασμό' : 'Καλεί σερβιτόρο' ?> · <?= e(ago($c['created_at'])) ?></div></div>
      <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="btn dark sm" style="height:44px">Εξυπηρετήθηκε</button></form>
    </div>
  <?php endforeach; ?>
  <?php if (!$open): ?><div class="card empty">Καμία κλήση. Η σελίδα ανανεώνεται μόνη της.</div><?php endif; ?>
</div>
<div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('bell', 'purple') ?><div><b>Άφησε αυτή τη σελίδα ανοιχτή</b><small>Σε tablet ή κινητό στο μπαρ. Κάθε νέα κλήση ακούγεται με ήχο.</small></div></div>
    <button class="btn" type="button" id="sound">🔔 Ενεργοποίηση ήχου</button>
  </div>
  <?php if ($done): ?>
    <p class="sechd">Εξυπηρετήθηκαν (3 ώρες)</p>
    <div class="card lst">
      <?php foreach ($done as $c): ?>
        <div class="lr"><span class="grow"><b><?= e($c['table_label']) ?></b><small><?= $c['kind'] === 'bill' ? 'Λογαριασμός' : 'Σερβιτόρος' ?> · <?= e(ago($c['done_at'])) ?><?= $c['by_name'] ? ' · ' . e($c['by_name']) : '' ?></small></span></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>
</div>
<script>
(function () {
  var last = <?= (int) $lastId ?>, feed = <?= json_encode(module_url('qr-boss', 'calls/feed')) ?>, ctx = null;
  var key = 'qr-sound';
  function beep() {
    try {
      ctx = ctx || new (window.AudioContext || window.webkitAudioContext)();
      [0, .25].forEach(function (t) {
        var o = ctx.createOscillator(), g = ctx.createGain();
        o.frequency.value = 880; o.connect(g); g.connect(ctx.destination);
        g.gain.setValueAtTime(.3, ctx.currentTime + t); g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + t + .2);
        o.start(ctx.currentTime + t); o.stop(ctx.currentTime + t + .2);
      });
    } catch (e) {}
  }
  document.getElementById('sound').addEventListener('click', function () { beep(); this.textContent = '🔔 Ο ήχος είναι ενεργός'; try { sessionStorage.setItem(key, '1'); } catch (e) {} });
  setInterval(function () {
    fetch(feed, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
      if (d.last > last) { beep(); setTimeout(function () { location.reload(); }, 600); }
    }).catch(function () {});
  }, 8000);
})();
</script>
