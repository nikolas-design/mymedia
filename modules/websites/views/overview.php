<?= module_tabs([['overview', '', 'Το site μου']], 'overview') ?>
<div class="cols">
<div>
  <div class="card">
    <div class="cardhd"><?= tile('globe', 'blue') ?><div><b><?= e($site['domain'] ?: 'Το site σου') ?></b><small><?= e($site['platform'] ?? '') ?></small></div>
      <?php if ($site['site_url']): ?><a class="btn sm" style="margin-left:auto" href="<?= e($site['site_url']) ?>" target="_blank" rel="noopener">Άνοιγμα ↗</a><?php endif; ?></div>
    <table class="tbl"><tbody>
      <tr><td>Domain</td><td class="n"><?= ws_expiry($site['domain_until']) ?></td></tr>
      <tr><td>Φιλοξενία</td><td class="n"><?= ws_expiry($site['hosting_until']) ?></td></tr>
      <tr><td>SSL (https)</td><td class="n"><?= ws_expiry($site['ssl_until']) ?></td></tr>
      <tr><td>Αλλαγές αυτόν τον μήνα</td><td class="n"><b><?= $used ?></b> / <?= (int) $site['changes_per_month'] ?></td></tr>
    </tbody></table>
    <?php if (!$site['domain']): ?><p class="small muted mb0">Τα στοιχεία του site θα τα συμπληρώσει η ομάδα μας.</p><?php endif; ?>
  </div>
  <p class="sechd">Τα αιτήματά σου</p>
  <div class="card lst">
    <?php foreach ($requests as $r): ?>
      <a class="lr" href="<?= e(module_url('websites', 'requests/' . $r['id'])) ?>">
        <span class="grow"><b><?= $r['urgent'] ? '⚡ ' : '' ?><?= e($r['title']) ?></b><small><?= e(ago($r['created_at'])) ?><?= $r['by_name'] ? ' · ' . e($r['by_name']) : '' ?><?= $r['reply'] ? ' · έχει απάντηση' : '' ?></small></span>
        <?= pill(...WS_STATUS[$r['status']]) ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$requests): ?><div class="empty">Κανένα αίτημα ακόμα.</div><?php endif; ?>
  </div>
</div>
<form class="card" method="post" enctype="multipart/form-data">
  <?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'blue') ?><div><b>Ζήτα αλλαγή στο site</b><small>Νέα φωτογραφία, αλλαγή τιμών, νέα σελίδα, διόρθωση…</small></div></div>
  <label>Τι θέλεις να αλλάξει</label><input name="title" required maxlength="160" placeholder="π.χ. Νέο μενού καλοκαιριού">
  <label>Περιγραφή</label><textarea name="description" required rows="5" placeholder="Γράψε όσο πιο συγκεκριμένα γίνεται. Μπορείς να επικολλήσεις και κείμενο."></textarea>
  <label>Σε ποια σελίδα (προαιρετικό)</label><input name="page_url" placeholder="π.χ. /menu ή η διεύθυνση της σελίδας">
  <label>Φωτογραφία ή screenshot</label><input type="file" name="photo" accept="image/*" style="height:auto;padding:10px">
  <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="urgent" value="1"> Επείγον</label>
  <button class="btn dark">Αποστολή</button>
  <p class="hint">Το πακέτο σου περιλαμβάνει <?= (int) $site['changes_per_month'] ?> αλλαγές τον μήνα.</p>
</form>
</div>
