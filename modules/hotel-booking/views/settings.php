<?= hb_tabs('settings') ?>
<?php $link = full_url('p/hotel-booking/' . $settings['code']); ?>
<div class="cols">
<div class="card">
  <div class="cardhd" style="margin-bottom:0"><?= tile('globe', 'green') ?><div><b>Η μηχανή κρατήσεών σου</b><small>Βάλε αυτό το κουμπί στο site σου: «Κάντε κράτηση».</small></div></div>
  <div class="copy mt"><input id="hblink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#hblink">Αντιγραφή</button></div>
  <a class="btn" href="<?= e($link) ?>" target="_blank" rel="noopener">Άνοιγμα σελίδας κρατήσεων</a>
  <p class="hint">Οι απευθείας κρατήσεις δεν έχουν προμήθεια. Κλείνεις εδώ τις ημέρες που πουλήθηκαν σε άλλες πλατφόρμες, για να μη γίνονται διπλοκρατήσεις.</p>
</div>
<form class="card" method="post"><?= csrf_field() ?>
  <label style="margin-top:0">Όνομα καταλύματος</label><input name="title" value="<?= e($settings['title']) ?>">
  <div class="row2"><div><label>Check-in από</label><input name="checkin" value="<?= e($settings['checkin']) ?>"></div><div><label>Check-out έως</label><input name="checkout" value="<?= e($settings['checkout']) ?>"></div></div>
  <div class="row2"><div><label>Ελάχιστα βράδια</label><input type="number" name="min_nights" value="<?= (int) $settings['min_nights'] ?>"></div><div><label>Προκαταβολή %</label><input type="number" name="deposit_percent" value="<?= (int) $settings['deposit_percent'] ?>"></div></div>
  <div class="row2"><div><label>Τηλέφωνο</label><input name="phone" value="<?= e($settings['phone']) ?>"></div><div><label>Email</label><input name="email" type="email" value="<?= e($settings['email']) ?>"></div></div>
  <label>Διεύθυνση</label><input name="address" value="<?= e($settings['address']) ?>">
  <label>Όροι κράτησης & ακύρωσης (και τρόπος πληρωμής προκαταβολής)</label><textarea name="policy" rows="4"><?= e($settings['policy']) ?></textarea>
  <div class="row2"><div><label>Χρώμα</label><input type="color" name="color" value="<?= e($settings['color']) ?>" style="width:64px;padding:4px"></div>
    <div><label style="display:flex;gap:8px;align-items:center;margin-top:36px"><input type="checkbox" name="auto_confirm" value="1" <?= $settings['auto_confirm'] ? 'checked' : '' ?>> Αυτόματη επιβεβαίωση</label></div></div>
  <button class="btn dark">Αποθήκευση</button>
</form>
</div>
