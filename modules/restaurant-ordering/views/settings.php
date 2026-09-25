<?= ro_tabs('settings') ?>
<script src="<?= e(asset('vendor/qrcode.js')) ?>" defer></script><script src="<?= e(asset('qr.js')) ?>" defer></script>
<?php $link = ro_public_url($settings['code']); $m = fn($c) => number_format($c / 100, 2, ',', ''); ?>
<div class="cols">
<div>
  <div class="card c">
    <div class="qrbox" id="roqr" style="width:200px" data-qr="<?= e($link) ?>"></div>
    <p class="small muted">Η σελίδα παραγγελιών σου. Βάλ' την στο Instagram, στο Google Business Profile, στο site και σε flyers.</p>
    <div class="copy" style="max-width:440px;margin:0 auto"><input id="rolink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#rolink">Αντιγραφή</button></div>
    <div class="btns" style="justify-content:center;margin-top:10px"><button class="btn sm" type="button" data-qr-download="png" data-qr-target="#roqr" data-qr-name="paraggelies">QR (PNG)</button><a class="btn sm" href="<?= e($link) ?>" target="_blank" rel="noopener">Άνοιγμα</a></div>
  </div>
</div>
<form class="card" method="post"><?= csrf_field() ?>
  <label style="display:flex;gap:8px;align-items:center;margin-top:0;font-size:15px"><input type="checkbox" name="accepting" value="1" <?= $settings['accepting'] ? 'checked' : '' ?>> <b>Δέχομαι παραγγελίες τώρα</b></label>
  <label>Τίτλος</label><input name="title" value="<?= e($settings['title']) ?>">
  <div class="btns mt"><label class="btn sm" style="font-weight:500;gap:6px"><input type="checkbox" name="delivery" value="1" <?= $settings['delivery'] ? 'checked' : '' ?>> Delivery</label><label class="btn sm" style="font-weight:500;gap:6px"><input type="checkbox" name="takeaway" value="1" <?= $settings['takeaway'] ? 'checked' : '' ?>> Take away</label></div>
  <div class="row2">
    <div><label>Ελάχιστη παραγγελία (€)</label><input name="min_order" value="<?= e($m($settings['min_order_cents'])) ?>" inputmode="decimal"></div>
    <div><label>Κόστος αποστολής (€)</label><input name="delivery_fee" value="<?= e($m($settings['delivery_fee_cents'])) ?>" inputmode="decimal"></div>
  </div>
  <label>Περιοχές delivery</label><input name="areas" value="<?= e($settings['areas']) ?>" placeholder="π.χ. Κέντρο, Κουκάκι, Παγκράτι">
  <div class="row2">
    <div><label>Συνήθης χρόνος (λεπτά)</label><input name="prep_minutes" type="number" value="<?= (int) $settings['prep_minutes'] ?>"></div>
    <div><label>Χρώμα</label><input type="color" name="color" value="<?= e($settings['color']) ?>" style="width:64px;padding:4px"></div>
  </div>
  <div class="row2"><div><label>Τηλέφωνο</label><input name="phone" value="<?= e($settings['phone']) ?>"></div><div><label>Διεύθυνση</label><input name="address" value="<?= e($settings['address']) ?>"></div></div>
  <label>Ωράριο</label><input name="hours" value="<?= e($settings['hours']) ?>" placeholder="π.χ. Καθημερινά 12:00 – 00:00">
  <label>Ανακοίνωση</label><input name="note" value="<?= e($settings['note']) ?>" placeholder="π.χ. Δωρεάν αναψυκτικό σε παραγγελίες άνω των 20€">
  <button class="btn dark">Αποθήκευση</button>
</form>
</div>
