<?= rb_tabs('locations') ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script><script src="<?= e(asset('qr.js')) ?>" defer></script>
<?php $link = rb_public_url($l['code']); ?>
<a class="back" href="<?= e(module_url('review-booster', 'locations')) ?>"><?= icon('back', 14) ?> Σημεία & QR</a>
<div class="cols">
<div>
  <div class="card c">
    <div class="qrbox" id="rbqr" style="width:240px;max-width:100%" data-qr="<?= e($link) ?>"></div>
    <h1 style="font-size:22px;margin:14px 0 4px"><?= e($l['name']) ?></h1>
    <p class="small muted" style="margin:0">Βάλε το QR στο ταμείο, στον λογαριασμό ή στο τραπέζι.</p>
    <div class="copy" style="max-width:420px;margin:12px auto 0"><input id="rblink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#rblink">Αντιγραφή</button></div>
    <div class="btns" style="justify-content:center;margin-top:12px">
      <button class="btn sm" type="button" data-qr-download="png" data-qr-target="#rbqr" data-qr-name="review-<?= e($l['code']) ?>">PNG</button>
      <button class="btn sm" type="button" data-qr-download="svg" data-qr-target="#rbqr" data-qr-name="review-<?= e($l['code']) ?>">SVG</button>
      <a class="btn sm" href="<?= e($link) ?>" target="_blank" rel="noopener">Δοκιμή</a>
    </div>
  </div>
  <div class="g2">
    <div class="stat"><small>Αξιολογήσεις 30 ημ.</small><b><?= $s['count'] ?></b></div>
    <div class="stat"><small>Μέσος όρος</small><b><?= $s['avg'] !== null ? number_format($s['avg'], 1, ',', '') : '—' ?></b></div>
  </div>
</div>
<div>
  <?php if ($canEdit): ?>
  <form class="card" method="post">
    <?= csrf_field() ?>
    <label style="margin-top:0">Όνομα σημείου</label><input name="name" value="<?= e($l['name']) ?>" required>
    <label>Σύνδεσμος κριτικής Google</label><input name="google_url" value="<?= e($l['google_url']) ?>" placeholder="https://g.page/r/…/review">
    <p class="hint">Google Business Profile → «Ζητήστε κριτικές» → αντιγραφή συνδέσμου.</p>
    <label>Στο Google οδηγούνται όσοι βάζουν</label>
    <select name="threshold"><option value="4" <?= (int) $l['threshold'] === 4 ? 'selected' : '' ?>>4 ή 5 αστέρια</option><option value="5" <?= (int) $l['threshold'] === 5 ? 'selected' : '' ?>>μόνο 5 αστέρια</option></select>
    <label>Ερώτηση</label><input name="question" value="<?= e($l['question']) ?>" placeholder="Πώς ήταν η εμπειρία σας;">
    <label>Μήνυμα ευχαριστίας</label><input name="thanks" value="<?= e($l['thanks']) ?>" placeholder="Ευχαριστούμε που μας επιλέξατε!">
    <label>Χρώμα</label><input type="color" name="color" value="<?= e($l['color']) ?>" style="width:64px;padding:4px">
    <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $l['active'] ? 'checked' : '' ?>> Ενεργό</label>
    <button class="btn dark">Αποθήκευση</button>
  </form>
  <form method="post" data-confirm="Διαγραφή του σημείου και των αξιολογήσεών του;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή σημείου</button></form>
  <?php endif; ?>
</div>
</div>
