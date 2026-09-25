<span class="eyebrow"><?= icon('user', 12) ?> Προφίλ</span>
<h1><?= e($me['name']) ?></h1>
<p class="lead">Συνδεδεμένος ως <?= e($me['email']) ?><?= $biz ? ' · ' . e($biz['name']) . ' (' . e(ROLE_LABELS[$biz['role']]) . ')' : '' ?></p>

<div class="cols">
<div>
  <!-- Μενού για το κινητό -->
  <div class="card lst">
    <?php
    $links = [];
    if ($biz) {
        $links[] = ['support', 'Υποστήριξη', 'headset'];
        if (has_role('owner')) $links[] = ['billing', 'Χρεώσεις', 'receipt'];
        if (has_role('owner', 'manager')) $links[] = ['business', 'Ρυθμίσεις επιχείρησης', 'settings'];
        $links[] = ['notifications', 'Ειδοποιήσεις', 'bell'];
    }
    if (is_admin()) {
        $links[] = ['admin', 'Διαχείριση πλατφόρμας', 'shield'];
        $links[] = ['admin/tickets', 'Admin · Υποστήριξη', 'headset'];
        $links[] = ['admin/tools', 'Admin · Εργαλεία & τιμές', 'grid'];
        $links[] = ['admin/settings', 'Admin · Ρυθμίσεις', 'settings'];
    }
    foreach ($links as [$p, $label, $ico]): ?>
      <a class="lr" href="<?= e(url($p)) ?>"><?= tile($ico, 'grey') ?><span class="grow"><b><?= e($label) ?></b></span><?= icon('chevron') ?></a>
    <?php endforeach; ?>
  </div>

  <?php if (count($businesses) > 1): ?>
    <form class="card" method="post" action="<?= e(url('switch')) ?>">
      <?= csrf_field() ?>
      <div class="cardhd" style="margin-bottom:0"><?= tile('swap', 'blue') ?><div><b>Αλλαγή επιχείρησης</b><small>Ανήκεις σε <?= count($businesses) ?> επιχειρήσεις.</small></div></div>
      <select name="business_id" class="mt">
        <?php foreach ($businesses as $b): ?><option value="<?= (int) $b['id'] ?>" <?= $biz && (int) $biz['id'] === (int) $b['id'] ? 'selected' : '' ?>><?= e($b['name']) ?></option><?php endforeach; ?>
      </select>
      <button class="btn" type="submit">Αλλαγή</button>
    </form>
  <?php endif; ?>

  <form method="post" action="<?= e(url('logout')) ?>"><?= csrf_field() ?><button class="btn danger" type="submit"><?= icon('logout', 16) ?> Αποσύνδεση</button></form>
</div>

<div>
  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="profile">
    <div class="cardhd" style="margin-bottom:0"><?= tile('user', 'purple') ?><div><b>Στοιχεία</b></div></div>
    <label for="name">Ονοματεπώνυμο</label>
    <input id="name" name="name" value="<?= e($me['name']) ?>" required>
    <label>Email</label>
    <input value="<?= e($me['email']) ?>" disabled>
    <button class="btn" type="submit">Αποθήκευση</button>
  </form>
  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="password">
    <div class="cardhd" style="margin-bottom:0"><?= tile('shield', 'green') ?><div><b>Αλλαγή κωδικού</b></div></div>
    <label for="current">Τρέχων κωδικός</label>
    <input id="current" name="current" type="password" autocomplete="current-password" required>
    <label for="password">Νέος κωδικός</label>
    <input id="password" name="password" type="password" autocomplete="new-password" minlength="10" required>
    <p class="hint">Τουλάχιστον 10 χαρακτήρες.</p>
    <button class="btn" type="submit">Αλλαγή κωδικού</button>
  </form>
</div>
</div>
