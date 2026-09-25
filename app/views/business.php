<span class="eyebrow"><?= icon('settings', 12) ?> Ρυθμίσεις επιχείρησης</span>
<h1>Τα στοιχεία <em>σου.</em></h1>
<p class="lead">Χρησιμοποιούνται στα παραστατικά και στην επικοινωνία μαζί σου.</p>
<form class="card" method="post" style="max-width:720px">
  <?= csrf_field() ?>
  <label for="name">Όνομα επιχείρησης (διακριτικός τίτλος)</label>
  <input id="name" name="name" value="<?= e($b['name']) ?>" required>
  <label for="legal_name">Επωνυμία</label>
  <input id="legal_name" name="legal_name" value="<?= e($b['legal_name']) ?>">
  <div class="row2">
    <div><label for="vat_number">ΑΦΜ</label><input id="vat_number" name="vat_number" value="<?= e($b['vat_number']) ?>" inputmode="numeric" pattern="\d{9}"></div>
    <div><label for="tax_office">ΔΟΥ</label><input id="tax_office" name="tax_office" value="<?= e($b['tax_office']) ?>"></div>
  </div>
  <label for="address">Διεύθυνση</label>
  <input id="address" name="address" value="<?= e($b['address']) ?>">
  <div class="row2">
    <div><label for="phone">Τηλέφωνο</label><input id="phone" name="phone" value="<?= e($b['phone']) ?>" type="tel"></div>
    <div><label for="billing_email">Email τιμολόγησης</label><input id="billing_email" name="billing_email" value="<?= e($b['billing_email']) ?>" type="email"></div>
  </div>
  <button class="btn dark" type="submit">Αποθήκευση</button>
</form>
