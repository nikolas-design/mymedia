<?php require __DIR__ . '/_tabs.php'; $link = qr_public_url($c['code']); ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script>
<script src="<?= e(asset('qr.js')) ?>" defer></script>
<a class="back" href="<?= e(module_url('qr-boss', 'codes')) ?>"><?= icon('back', 14) ?> Κωδικοί QR</a>

<div class="cols">
<div>
  <div class="card c">
    <div class="qrbox" id="bigqr" style="width:260px;max-width:100%" data-qr="<?= e($link) ?>" data-qr-size="240" data-qr-color="<?= e($profile['color']) ?>"></div>
    <h1 style="font-size:22px;margin:14px 0 4px"><?= e($c['name']) ?></h1>
    <p class="small muted" style="margin:0"><?= e(qr_type_label($c['type'])) ?><?= $c['active'] ? '' : ' · ανενεργό' ?></p>
    <div class="copy" style="max-width:420px;margin:12px auto 0"><input id="qrlink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#qrlink">Αντιγραφή</button></div>
    <div class="btns" style="justify-content:center;margin-top:12px">
      <button class="btn sm" type="button" data-qr-download="png" data-qr-target="#bigqr" data-qr-name="<?= e('qr-' . $c['code']) ?>">PNG (για εκτύπωση)</button>
      <button class="btn sm" type="button" data-qr-download="svg" data-qr-target="#bigqr" data-qr-name="<?= e('qr-' . $c['code']) ?>">SVG (για γραφίστα)</button>
      <a class="btn sm" href="<?= e(module_url('qr-boss', 'print?ids[]=' . $c['id'])) ?>" target="_blank"><?= icon('print', 14) ?> Εκτύπωση</a>
      <a class="btn sm" href="<?= e($link) ?>" target="_blank" rel="noopener">Δοκιμή</a>
    </div>
  </div>
  <div class="card">
    <div class="cardhd"><?= tile('chart', 'blue') ?><div><b>Σαρώσεις</b><small><?= $total ?> συνολικά · τελευταίες 14 ημέρες</small></div></div>
    <?= qr_bars($daily) ?>
  </div>
</div>

<div>
  <?php if ($canEdit): ?>
    <form class="card" method="post">
      <?= csrf_field() ?>
      <div class="cardhd" style="margin-bottom:0"><?= tile('settings', 'purple') ?><div><b>Τι ανοίγει αυτό το QR</b><small>Αλλάζει αμέσως, χωρίς νέα εκτύπωση.</small></div></div>
      <label>Όνομα</label><input name="name" value="<?= e($c['name']) ?>" required>
      <label>Τύπος</label>
      <select name="type" id="qrtype">
        <?php foreach (QR_TYPES as $k => [$label]): ?><option value="<?= $k ?>" <?= $c['type'] === $k ? 'selected' : '' ?>><?= e($label) ?></option><?php endforeach; ?>
      </select>
      <div data-show="link"><label>Σύνδεσμος</label><input name="target_url" value="<?= e($c['target_url']) ?>" placeholder="https://…"></div>
      <div data-show="menu"><label>Τραπέζι</label><input name="table_label" value="<?= e($c['table_label']) ?>" placeholder="π.χ. Τραπέζι 5">
        <p class="hint">Με τραπέζι, ο πελάτης μπορεί να καλέσει σερβιτόρο<?= $pro ? '' : ' (Pro)' ?>.</p></div>
      <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $c['active'] ? 'checked' : '' ?>> Ενεργό</label>
      <button class="btn dark">Αποθήκευση</button>
    </form>
    <form method="post" data-confirm="Διαγραφή του QR; Αν είναι τυπωμένο, δεν θα ανοίγει πια.">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <button class="btn danger">Διαγραφή QR</button>
    </form>
  <?php endif; ?>
</div>
</div>
