<span class="eyebrow"><?= icon('shield', 12) ?> Είσοδος πελάτη</span>
<h1>Καλώς ήρθες <em>πίσω.</em></h1>
<p class="lead">Συνδέσου για να δεις τα εργαλεία, τα αιτήματα και τις χρεώσεις της επιχείρησής σου.</p>
<?php if ($error): ?><div class="flash error"><?= e($error) ?></div><?php endif; ?>
<form class="card" method="post">
  <?= csrf_field() ?>
  <label for="email">Email</label>
  <input id="email" name="email" type="email" value="<?= e($email) ?>" autocomplete="username" required autofocus>
  <label for="password">Κωδικός</label>
  <input id="password" name="password" type="password" autocomplete="current-password" required>
  <button class="btn dark" type="submit">Σύνδεση</button>
  <p class="c small mt mb0"><a href="<?= e(url('forgot')) ?>">Ξέχασες τον κωδικό;</a></p>
</form>
<p class="c small muted mt">Δεν έχεις λογαριασμό; Η πρόσβαση γίνεται με πρόσκληση από την επιχείρησή σου ή από εμάς.</p>
