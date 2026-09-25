<h1>Λυπούμαστε γι' αυτό</h1>
<p class="lead">Πείτε μας τι δεν πήγε καλά. Το μήνυμα το διαβάζει απευθείας ο υπεύθυνος και θα το διορθώσουμε.</p>
<form method="post" action="<?= e(url('r/' . $loc['code'] . '/feedback')) ?>" class="pcard">
  <?= csrf_field() ?>
  <label style="margin-top:0">Τι έγινε;</label>
  <textarea name="message" required maxlength="3000" autofocus></textarea>
  <div class="row2">
    <div><label>Όνομα (προαιρετικό)</label><input name="name" autocomplete="name"></div>
    <div><label>Τηλέφωνο ή email</label><input name="contact" placeholder="για να σας απαντήσουμε"></div>
  </div>
  <button class="cta">Αποστολή</button>
</form>
