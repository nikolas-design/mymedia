<a class="back" href="<?= e(url('admin/t/websites')) ?>"><?= icon('back', 14) ?> Websites</a>
<h1><?= e($b['name']) ?></h1>
<form class="card" method="post" style="max-width:720px"><?= csrf_field() ?>
  <div class="row2"><div><label style="margin-top:0">Domain</label><input name="domain" value="<?= e($site['domain']) ?>" placeholder="karagiozisclub.gr"></div><div><label style="margin-top:0">URL</label><input name="site_url" value="<?= e($site['site_url']) ?>" placeholder="https://…"></div></div>
  <label>Πλατφόρμα</label><input name="platform" value="<?= e($site['platform']) ?>" placeholder="π.χ. WordPress, στατικό">
  <div class="row2"><div><label>Λήξη domain</label><input type="date" name="domain_until" value="<?= e($site['domain_until']) ?>"></div><div><label>Λήξη φιλοξενίας</label><input type="date" name="hosting_until" value="<?= e($site['hosting_until']) ?>"></div></div>
  <div class="row2"><div><label>Λήξη SSL</label><input type="date" name="ssl_until" value="<?= e($site['ssl_until']) ?>"></div><div><label>Αλλαγές / μήνα</label><input type="number" name="changes_per_month" value="<?= (int) $site['changes_per_month'] ?>"></div></div>
  <label>Εσωτερικές σημειώσεις (FTP, πρόσβαση κ.λπ. — όχι κωδικοί)</label><textarea name="notes" rows="3"><?= e($site['notes']) ?></textarea>
  <button class="btn dark">Αποθήκευση</button>
</form>
