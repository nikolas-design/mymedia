<h1>Επιχειρήσεις</h1>
<div class="cols">
<div>
  <form method="get" class="inline" style="margin-bottom:12px">
    <input name="q" value="<?= e($search) ?>" placeholder="Αναζήτηση: όνομα, επωνυμία, ΑΦΜ">
    <button class="btn sm auto" style="height:44px">Αναζήτηση</button>
  </form>
  <div class="card lst">
    <?php foreach ($businesses as $b): ?>
      <a class="lr" href="<?= e(url('admin/businesses/' . $b['id'])) ?>">
        <span class="av" style="<?= avatar_style($b['name']) ?>"><?= e(initials($b['name'])) ?></span>
        <span class="grow"><b><?= e($b['name']) ?></b><small><?= (int) $b['subs'] ?> εργαλεία · <?= (int) $b['members'] ?> μέλη<?= $b['vat_number'] ? ' · ΑΦΜ ' . e($b['vat_number']) : '' ?></small></span>
        <?php if ($b['unpaid']): ?><span class="r"><b><?= money($b['unpaid']) ?></b><?= pill('Ανείσπρακτο', 'orange') ?></span><?php endif; ?>
        <?= icon('chevron') ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$businesses): ?><div class="empty">Καμία επιχείρηση.</div><?php endif; ?>
  </div>
</div>
<form class="card" method="post">
  <?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'purple') ?><div><b>Νέα επιχείρηση</b><small>Ο ιδιοκτήτης λαμβάνει πρόσκληση για να φτιάξει κωδικό.</small></div></div>
  <label for="name">Όνομα επιχείρησης</label>
  <input id="name" name="name" required>
  <label for="vat_number">ΑΦΜ (προαιρετικό)</label>
  <input id="vat_number" name="vat_number" inputmode="numeric">
  <label for="owner_email">Email ιδιοκτήτη</label>
  <input id="owner_email" name="owner_email" type="email">
  <button class="btn dark" type="submit">Δημιουργία</button>
</form>
</div>
