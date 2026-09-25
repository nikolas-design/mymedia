<a class="back" href="<?= e(url('login')) ?>"><?= icon('back', 14) ?> Είσοδος</a>
<h1>Ξέχασες τον <em>κωδικό;</em></h1>
<?php if ($sent): ?>
  <div class="flash ok">Αν υπάρχει λογαριασμός με αυτό το email, σου στείλαμε σύνδεσμο για νέο κωδικό. Κοίτα και στα ανεπιθύμητα.</div>
<?php else: ?>
  <p class="lead">Γράψε το email σου και θα σου στείλουμε σύνδεσμο για να ορίσεις νέο κωδικό.</p>
  <form class="card" method="post">
    <?= csrf_field() ?>
    <label for="email">Email</label>
    <input id="email" name="email" type="email" required autofocus>
    <button class="btn dark" type="submit">Αποστολή συνδέσμου</button>
  </form>
<?php endif; ?>
