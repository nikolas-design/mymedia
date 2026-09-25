<span class="eyebrow"><?= icon('mail', 12) ?> Πρόσκληση</span>
<h1>Μπες στην ομάδα της <em><?= e($inv['business_name']) ?>.</em></h1>
<p class="lead">Ρόλος: <b><?= e(ROLE_LABELS[$inv['role']]) ?></b><?= $inv['job_title'] ? ' · ' . e($inv['job_title']) : '' ?></p>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>

<?php if ($existing): ?>
  <?php if ($me && (int) $me['id'] === (int) $existing['id']): ?>
    <form class="card" method="post">
      <?= csrf_field() ?>
      <p class="body mb0">Είσαι συνδεδεμένος ως <b><?= e($me['email']) ?></b>.</p>
      <button class="btn dark" type="submit">Αποδοχή πρόσκλησης</button>
    </form>
  <?php else: ?>
    <div class="card">
      <p class="body">Υπάρχει ήδη λογαριασμός για το <b><?= e($inv['email']) ?></b>. Συνδέσου με αυτόν και άνοιξε ξανά τον σύνδεσμο της πρόσκλησης.</p>
      <?php $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? ''; ?>
      <a class="btn dark" href="<?= e(url('login')) ?>">Σύνδεση</a>
    </div>
  <?php endif; ?>
<?php else: ?>
  <form class="card" method="post">
    <?= csrf_field() ?>
    <label>Email</label>
    <input value="<?= e($inv['email']) ?>" disabled>
    <label for="name">Ονοματεπώνυμο</label>
    <input id="name" name="name" autocomplete="name" required autofocus>
    <label for="password">Κωδικός</label>
    <input id="password" name="password" type="password" autocomplete="new-password" minlength="10" required>
    <p class="hint">Τουλάχιστον 10 χαρακτήρες.</p>
    <button class="btn dark" type="submit">Δημιουργία λογαριασμού</button>
  </form>
<?php endif; ?>
