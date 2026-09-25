<?= ro_tabs('orders') ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px">
  <span class="small muted">Σήμερα: <b><?= (int) $today['n'] ?></b> παραγγελίες · <b><?= money($today['total']) ?></b></span>
  <div class="btns">
    <?= $settings['accepting'] ? pill('Δέχεστε παραγγελίες', 'green') : pill('Κλειστά για παραγγελίες', 'red') ?>
    <button class="btn sm" type="button" id="sound">🔔 Ήχος</button>
  </div>
</div>
<div class="cols" style="grid-template-columns:repeat(auto-fill,minmax(300px,1fr))">
  <?php foreach ($active as $o): ?>
    <div class="card" style="<?= $o['status'] === 'new' ? 'border-color:var(--warn);box-shadow:0 0 0 1px var(--warn)' : '' ?>">
      <div style="display:flex;justify-content:space-between;align-items:center;gap:8px">
        <b style="font-size:18px">#<?= (int) $o['number'] ?> · <?= e(RO_KIND[$o['kind']]) ?></b><?= pill(...RO_STATUS[$o['status']]) ?>
      </div>
      <p class="small muted" style="margin:4px 0 8px"><?= e(date('H:i', strtotime($o['created_at']))) ?> · <?= e($o['name']) ?> · <a href="tel:<?= e($o['phone']) ?>"><?= e($o['phone']) ?></a></p>
      <?php foreach ($lines[(int) $o['id']] ?? [] as $l): ?><div class="small" style="display:flex;justify-content:space-between"><span><b><?= (int) $l['qty'] ?>×</b> <?= e($l['name']) ?><?= $l['note'] ? ' <i class="muted">(' . e($l['note']) . ')</i>' : '' ?></span><span><?= money($l['qty'] * $l['price_cents']) ?></span></div><?php endforeach; ?>
      <div class="small" style="display:flex;justify-content:space-between;margin-top:6px;border-top:1px solid var(--line);padding-top:6px"><b>Σύνολο (<?= $o['payment'] === 'card' ? 'κάρτα' : 'μετρητά' ?>)</b><b><?= money($o['total_cents']) ?></b></div>
      <?php if ($o['kind'] === 'delivery'): ?><p class="small" style="margin:6px 0 0">📍 <?= e($o['address']) ?><?= $o['floor_bell'] ? ' · ' . e($o['floor_bell']) : '' ?></p><?php endif; ?>
      <?php if ($o['notes']): ?><p class="small" style="margin:6px 0 0">📝 <?= e($o['notes']) ?></p><?php endif; ?>
      <form method="post" action="<?= e(module_url('restaurant-ordering', 'orders/' . $o['id'])) ?>" class="btns mt"><?= csrf_field() ?>
        <?php if ($o['status'] === 'new'): ?>
          <?php foreach ([15, 30, 45, 60] as $m): ?><button class="btn sm<?= $m === (int) $settings['prep_minutes'] ? ' dark' : '' ?>" name="eta" value="<?= $m ?>"><?= $m ?>'</button><?php endforeach; ?>
          <input type="hidden" name="do" value="accept">
        <?php elseif ($o['status'] === 'accepted'): ?>
          <button class="btn sm dark" name="do" value="<?= $o['kind'] === 'delivery' ? 'out' : 'ready' ?>"><?= $o['kind'] === 'delivery' ? '🛵 Στάλθηκε' : '✓ Έτοιμη' ?></button>
        <?php else: ?>
          <button class="btn sm dark" name="do" value="completed">✓ Ολοκληρώθηκε</button>
        <?php endif; ?>
        <a class="btn sm" href="<?= e(module_url('restaurant-ordering', 'orders/' . $o['id'])) ?>">Λεπτομέρειες</a>
      </form>
    </div>
  <?php endforeach; ?>
</div>
<?php if (!$active): ?><div class="card empty">Καμία ενεργή παραγγελία. Η σελίδα ανανεώνεται μόνη της.</div><?php endif; ?>
<script>
(function () {
  var last = <?= (int) $lastId ?>, feed = <?= json_encode(module_url('restaurant-ordering', 'feed')) ?>, ctx = null;
  function beep() { try { ctx = ctx || new (window.AudioContext || window.webkitAudioContext)();
    [0, .25, .5].forEach(function (t) { var o = ctx.createOscillator(), g = ctx.createGain(); o.frequency.value = 988; o.connect(g); g.connect(ctx.destination);
      g.gain.setValueAtTime(.3, ctx.currentTime + t); g.gain.exponentialRampToValueAtTime(.001, ctx.currentTime + t + .2); o.start(ctx.currentTime + t); o.stop(ctx.currentTime + t + .2); }); } catch (e) {} }
  document.getElementById('sound').addEventListener('click', function () { beep(); this.textContent = '🔔 Ήχος ενεργός'; });
  setInterval(function () { fetch(feed, { credentials: 'same-origin' }).then(function (r) { return r.json(); }).then(function (d) {
    if (d.last > last) { beep(); setTimeout(function () { location.reload(); }, 700); } }).catch(function () {}); }, 8000);
})();
</script>
