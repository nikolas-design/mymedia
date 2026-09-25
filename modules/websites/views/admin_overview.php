<a class="back" href="<?= e(url('admin/tools')) ?>"><?= icon('back', 14) ?> Εργαλεία</a>
<h1>Websites · Διαχείριση</h1>
<div class="cols">
<div>
  <p class="sechd" style="margin-top:0">Ανοιχτά αιτήματα (<?= count($open) ?>)</p>
  <div class="card lst">
    <?php foreach ($open as $r): ?>
      <a class="lr" href="<?= e(url('admin/t/websites/' . $r['id'])) ?>"><span class="grow"><b><?= $r['urgent'] ? '⚡ ' : '' ?><?= e($r['business_name']) ?> · <?= e($r['title']) ?></b><small><?= e(ago($r['created_at'])) ?></small></span><?= pill(...WS_STATUS[$r['status']]) ?></a>
    <?php endforeach; ?>
    <?php if (!$open): ?><div class="empty">Κανένα ανοιχτό αίτημα.</div><?php endif; ?>
  </div>
</div>
<div>
  <p class="sechd" style="margin-top:0">Sites πελατών</p>
  <div class="card scrollx"><table class="tbl"><thead><tr><th>Πελάτης</th><th>Domain</th><th>Φιλοξενία</th><th>SSL</th></tr></thead><tbody>
    <?php foreach ($sites as $s): ?>
      <tr><td><a href="<?= e(url('admin/t/websites/site/' . $s['id'])) ?>"><?= e($s['name']) ?></a><br><span class="small muted"><?= e($s['domain'] ?? '—') ?></span></td><td class="small"><?= ws_expiry($s['domain_until']) ?></td><td class="small"><?= ws_expiry($s['hosting_until']) ?></td><td class="small"><?= ws_expiry($s['ssl_until']) ?></td></tr>
    <?php endforeach; ?>
    <?php if (!$sites): ?><tr><td colspan="4" class="empty">Κανένας πελάτης με Websites.</td></tr><?php endif; ?>
  </tbody></table></div>
</div>
</div>
