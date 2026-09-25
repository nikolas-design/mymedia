<?php require __DIR__ . '/_tabs.php'; $p = $profile; ?>
<form method="post" enctype="multipart/form-data">
<?= csrf_field() ?>
<div class="cols">
<div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('logo', 'purple') ?><div><b>Ταυτότητα</b><small>Έτσι φαίνεται το μενού στους πελάτες σου.</small></div></div>
    <label>Τίτλος</label><input name="title" value="<?= e($p['title']) ?>" required>
    <label>Υπότιτλος</label><input name="subtitle" value="<?= e($p['subtitle']) ?>" placeholder="π.χ. Καφέ · Brunch · Cocktails">
    <label>Χρώμα</label>
    <div class="inline"><input type="color" name="color" value="<?= e($p['color']) ?>" style="width:64px;padding:4px"><span class="small muted">Κουμπιά, τίτλοι και τιμές</span></div>
    <label>Λογότυπο</label>
    <?php if ($p['logo']): ?><div class="inline" style="margin-bottom:8px;align-items:center"><img src="<?= e(upload_url($p['logo'])) ?>" alt="" style="height:48px"><label style="margin:0;font-weight:400"><input type="checkbox" name="remove_logo" value="1"> Αφαίρεση</label></div><?php endif; ?>
    <input type="file" name="logo" accept="image/jpeg,image/png,image/webp" style="height:auto;padding:10px">
    <label>Φωτογραφία εξωφύλλου</label>
    <?php if ($p['cover']): ?><div class="inline" style="margin-bottom:8px;align-items:center"><img src="<?= e(upload_url($p['cover'])) ?>" alt="" style="height:48px;border-radius:6px"><label style="margin:0;font-weight:400"><input type="checkbox" name="remove_cover" value="1"> Αφαίρεση</label></div><?php endif; ?>
    <input type="file" name="cover" accept="image/jpeg,image/png,image/webp" style="height:auto;padding:10px">
  </div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('bell', 'pink') ?><div><b>Κλήση σερβιτόρου</b><small><?= $pro ? 'Εμφανίζεται στα QR που έχουν τραπέζι.' : 'Διαθέσιμο στο πλάνο Pro.' ?></small></div></div>
    <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="waiter_calls" value="1" <?= $p['waiter_calls'] ? 'checked' : '' ?> <?= $pro ? '' : 'disabled' ?>> Ενεργή</label>
  </div>
</div>
<div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('building', 'blue') ?><div><b>Πληροφορίες</b></div></div>
    <label>Ωράριο</label><input name="hours" value="<?= e($p['hours']) ?>" placeholder="π.χ. Καθημερινά 08:00 – 01:00">
    <div class="row2">
      <div><label>Τηλέφωνο</label><input name="phone" value="<?= e($p['phone']) ?>" type="tel"></div>
      <div><label>Διεύθυνση</label><input name="address" value="<?= e($p['address']) ?>"></div>
    </div>
    <div class="row2">
      <div><label>Wi-Fi (όνομα)</label><input name="wifi_ssid" value="<?= e($p['wifi_ssid']) ?>"></div>
      <div><label>Wi-Fi (κωδικός)</label><input name="wifi_pass" value="<?= e($p['wifi_pass']) ?>"></div>
    </div>
    <label>Instagram</label><input name="instagram" value="<?= e($p['instagram']) ?>" placeholder="https://instagram.com/…">
    <label>Facebook</label><input name="facebook" value="<?= e($p['facebook']) ?>" placeholder="https://facebook.com/…">
    <label>Σύνδεσμος κριτικής Google</label><input name="google_review_url" value="<?= e($p['google_review_url']) ?>" placeholder="https://g.page/r/…/review">
    <p class="hint">Google Business Profile → «Ζητήστε κριτικές» → αντιγραφή συνδέσμου.</p>
    <label>Κείμενο στο τέλος του μενού</label><input name="footer" value="<?= e($p['footer']) ?>" placeholder="π.χ. Οι τιμές περιλαμβάνουν ΦΠΑ. Υπεύθυνος αγορανομίας: …">
  </div>
  <button class="btn dark">Αποθήκευση</button>
</div>
</div>
</form>
