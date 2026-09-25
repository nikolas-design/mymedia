<?php require __DIR__ . '/_tabs.php'; ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script>
<script src="<?= e(asset('qr.js')) ?>" defer></script>

<div class="cols">
<div>
  <form method="get" action="<?= e(module_url('qr-boss', 'print')) ?>" target="_blank" id="printform">
  <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
    <p class="sechd" style="margin:0">Οι κωδικοί σου (<?= count($codes) ?>)</p>
    <?php if ($codes): ?><button class="btn sm" type="submit"><?= icon('print', 14) ?> Εκτύπωση επιλεγμένων</button><?php endif; ?>
  </div>
  <div class="card lst">
    <?php foreach ($codes as $c): ?>
      <div class="lr<?= $c['active'] ? '' : ' off' ?>">
        <input type="checkbox" name="ids[]" value="<?= (int) $c['id'] ?>" <?= $c['active'] ? 'checked' : '' ?> aria-label="Επιλογή για εκτύπωση">
        <a href="<?= e(module_url('qr-boss', 'codes/' . $c['id'])) ?>" class="qrbox" style="width:56px;padding:4px" data-qr="<?= e(qr_public_url($c['code'])) ?>" data-qr-size="48"></a>
        <a class="grow" href="<?= e(module_url('qr-boss', 'codes/' . $c['id'])) ?>" style="color:inherit;min-width:0">
          <b><?= e($c['name']) ?></b>
          <small><?= e(qr_type_label($c['type'])) ?><?= $c['table_label'] && $c['table_label'] !== $c['name'] ? ' · ' . e($c['table_label']) : '' ?> · <?= (int) $c['n'] ?> σαρώσεις<?= $c['active'] ? '' : ' · ανενεργό' ?></small>
        </a>
        <?= icon('chevron') ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$codes): ?><div class="empty">Δεν έχεις ακόμα QR. Φτιάξε ένα ή πολλά μαζί για τα τραπέζια.</div><?php endif; ?>
  </div>
  </form>
</div>

<div>
  <?php if ($canEdit): ?>
    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="tables">
      <div class="cardhd" style="margin-bottom:0"><?= tile('grid', 'purple') ?><div><b>QR για τραπέζια</b><small>Ένα QR μενού ανά τραπέζι, με κλήση σερβιτόρου<?= $pro ? '' : ' (Pro)' ?>.</small></div></div>
      <div class="row2">
        <div><label>Από</label><input name="from" type="number" min="1" value="1"></div>
        <div><label>Έως</label><input name="to" type="number" min="1" value="10"></div>
      </div>
      <label>Ονομασία</label><input name="prefix" value="Τραπέζι">
      <button class="btn dark">Δημιουργία</button>
    </form>

    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="single">
      <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'blue') ?><div><b>Νέο QR</b><small>Για βιτρίνα, έντυπα, social ή κριτικές.</small></div></div>
      <label>Τι ανοίγει</label>
      <select name="type" id="qrtype">
        <?php foreach (QR_TYPES as $k => [$label]): ?><option value="<?= $k ?>"><?= e($label) ?></option><?php endforeach; ?>
      </select>
      <label>Όνομα (για εσένα)</label><input name="name" placeholder="π.χ. Βιτρίνα, Flyer Αυγούστου">
      <div data-show="link"><label>Σύνδεσμος</label><input name="target_url" placeholder="https://…"></div>
      <div data-show="menu"><label>Τραπέζι (προαιρετικό)</label><input name="table_label" placeholder="π.χ. Μπαλκόνι 3"></div>
      <p class="hint" data-show="wifi">Το όνομα και ο κωδικός του Wi-Fi ορίζονται στην Εμφάνιση.</p>
      <p class="hint" data-show="review">Ο σύνδεσμος κριτικής Google ορίζεται στην Εμφάνιση.</p>
      <button class="btn">Δημιουργία</button>
    </form>
  <?php endif; ?>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('bolt', 'green') ?><div><b>Δυναμικά QR</b><small>Αλλάζεις τι ανοίγει ένα QR όποτε θέλεις, χωρίς να το ξανατυπώσεις.</small></div></div>
  </div>
</div>
</div>
