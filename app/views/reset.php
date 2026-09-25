<h1>Νέος <em>κωδικός.</em></h1>
<p class="lead">Για τον λογαριασμό <?= e($email) ?></p>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <?= csrf_field() ?>
  <label for="password">Νέος κωδικός</label>
  <input id="password" name="password" type="password" autocomplete="new-password" minlength="10" required>
  <p class="hint">Τουλάχιστον 10 χαρακτήρες.</p>
  <label for="password2">Ξανά ο νέος κωδικός</label>
  <input id="password2" name="password2" type="password" autocomplete="new-password" minlength="10" required>
  <button class="btn dark" type="submit">Αποθήκευση</button>
</form>
