<p class="big"><?= in_array($o['status'], ['rejected'], true) ? '😔' : '🧾' ?></p>
<h1>Παραγγελία #<?= (int) $o['number'] ?></h1>
<p class="lead" id="stxt"><?= e(ro_customer_status($o)) ?></p>
<div class="pcard"><div class="list">
  <?php foreach ($lines as $l): ?><div class="it"><span><?= (int) $l['qty'] ?>× <?= e($l['name']) ?></span><span><?= money($l['qty'] * $l['price_cents']) ?></span></div><?php endforeach; ?>
  <?php if ($o['fee_cents']): ?><div class="it"><span>Αποστολή</span><span><?= money($o['fee_cents']) ?></span></div><?php endif; ?>
  <div class="it"><b>Σύνολο · <?= $o['payment'] === 'card' ? 'κάρτα' : 'μετρητά' ?></b><b><?= money($o['total_cents']) ?></b></div>
</div></div>
<?php if ($st['phone']): ?><p class="small muted">Για οτιδήποτε: <a href="tel:<?= e($st['phone']) ?>"><?= e($st['phone']) ?></a></p><?php endif; ?>
<a class="cta ghost" href="<?= e(url($base)) ?>">Νέα παραγγελία</a>
<?php if (!in_array($o['status'], ['completed', 'rejected'], true)): ?>
<script>setInterval(function () { fetch('?json=1').then(function (r) { return r.json(); }).then(function (d) { var el = document.getElementById('stxt'); if (el.textContent !== d.text) { el.textContent = d.text; if (navigator.vibrate) navigator.vibrate(200); } }); }, 10000);</script>
<?php endif; ?>
