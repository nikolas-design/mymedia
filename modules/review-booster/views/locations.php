<?= rb_tabs('locations') ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script><script src="<?= e(asset('qr.js')) ?>" defer></script>
<div class="cols">
<div class="card lst">
  <?php foreach ($locations as $l): ?>
    <a class="lr<?= $l['active'] ? '' : ' off' ?>" href="<?= e(module_url('review-booster', 'locations/' . $l['id'])) ?>">
      <span class="qrbox" style="width:56px;padding:4px" data-qr="<?= e(rb_public_url($l['code'])) ?>"></span>
      <span class="grow"><b><?= e($l['name']) ?></b><small><?= (int) $l['n'] ?> αξιολογήσεις (30 ημ.)<?= $l['avg'] ? ' · ' . number_format((float) $l['avg'], 1, ',', '') . ' ★' : '' ?><?= $l['google_url'] ? '' : ' · χωρίς σύνδεσμο Google' ?></small></span>
      <?= icon('chevron') ?>
    </a>
  <?php endforeach; ?>
</div>
<div>
  <?php if ($canEdit && count($locations) < $limit): ?>
    <form class="card" method="post">
      <?= csrf_field() ?>
      <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'purple') ?><div><b>Νέο σημείο</b><small>Π.χ. δεύτερο κατάστημα. Έως <?= $limit ?> στο πλάνο σου.</small></div></div>
      <label>Όνομα</label><input name="name" required placeholder="π.χ. Κατάστημα Γλυφάδας">
      <button class="btn dark">Προσθήκη</button>
    </form>
  <?php elseif (count($locations) >= $limit && $limit === 1): ?>
    <div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('building', 'grey') ?><div><b>Περισσότερα σημεία;</b><small>Το Pro περιλαμβάνει έως 3 τοποθεσίες με ξεχωριστά στατιστικά.</small></div></div>
      <a class="btn" href="<?= e(url('tools/review-booster')) ?>">Αναβάθμιση σε Pro</a></div>
  <?php endif; ?>
</div>
</div>
