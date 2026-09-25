<?= ap_tabs('settings') ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script><script src="<?= e(asset('qr.js')) ?>" defer></script>
<?php $link = ap_public_url($settings['code']); ?>
<div class="cols">
<div>
  <div class="card c">
    <div class="qrbox" id="apqr" style="width:200px" data-qr="<?= e($link) ?>"></div>
    <p class="small muted">Βάλε τον σύνδεσμο στο Instagram, στο Google Business Profile και στο site σου.</p>
    <div class="copy" style="max-width:440px;margin:0 auto"><input id="aplink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#aplink">Αντιγραφή</button></div>
    <div class="btns" style="justify-content:center;margin-top:10px"><button class="btn sm" type="button" data-qr-download="png" data-qr-target="#apqr" data-qr-name="rantevou">QR (PNG)</button><a class="btn sm" href="<?= e($link) ?>" target="_blank" rel="noopener">Άνοιγμα σελίδας</a></div>
  </div>
</div>
<form class="card" method="post"><?= csrf_field() ?>
  <label style="margin-top:0">Τίτλος</label><input name="title" value="<?= e($settings['title']) ?>">
  <label>Καλωσόρισμα</label><input name="intro" value="<?= e($settings['intro']) ?>" placeholder="π.χ. Κλείσε το ραντεβού σου σε 30 δευτερόλεπτα">
  <div class="row2"><div><label>Τηλέφωνο</label><input name="phone" value="<?= e($settings['phone']) ?>"></div><div><label>Διεύθυνση</label><input name="address" value="<?= e($settings['address']) ?>"></div></div>
  <div class="row2">
    <div><label>Ώρες ανά</label><select name="slot_minutes"><?php foreach ([5, 10, 15, 20, 30, 60] as $m): ?><option value="<?= $m ?>" <?= (int) $settings['slot_minutes'] === $m ? 'selected' : '' ?>><?= $m ?> λεπτά</option><?php endforeach; ?></select></div>
    <div><label>Χρώμα</label><input type="color" name="color" value="<?= e($settings['color']) ?>" style="width:64px;padding:4px"></div>
  </div>
  <div class="row2">
    <div><label>Κράτηση τουλάχιστον (ώρες πριν)</label><input name="min_notice_hours" type="number" min="0" value="<?= (int) $settings['min_notice_hours'] ?>"></div>
    <div><label>Έως πόσες ημέρες μπροστά</label><input name="max_days_ahead" type="number" min="1" value="<?= (int) $settings['max_days_ahead'] ?>"></div>
  </div>
  <label>Ακύρωση από τον πελάτη έως (ώρες πριν)</label><input name="cancel_hours" type="number" min="0" value="<?= (int) $settings['cancel_hours'] ?>" style="max-width:160px">
  <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="auto_confirm" value="1" <?= $settings['auto_confirm'] ? 'checked' : '' ?>> Αυτόματη επιβεβαίωση (αλλιώς τα εγκρίνεις εσύ)</label>
  <button class="btn dark">Αποθήκευση</button>
</form>
</div>
