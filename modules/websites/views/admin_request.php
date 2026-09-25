<a class="back" href="<?= e(url('admin/t/websites')) ?>"><?= icon('back', 14) ?> Websites</a>
<div class="cols">
  <div class="card">
    <div class="cardhd"><?= tile('globe', 'blue') ?><div><b><?= $r['urgent'] ? '⚡ ' : '' ?><?= e($r['title']) ?></b><small><?= e($r['business_name']) ?> · <?= e($r['by_name'] ?? '') ?> · <?= e(date_gr($r['created_at'])) ?></small></div></div>
    <?php if ($r['page_url']): ?><p class="small">Σελίδα: <?= e($r['page_url']) ?></p><?php endif; ?>
    <p class="body pre"><?= e($r['description']) ?></p>
    <?php if ($r['photo']): ?><a href="<?= e(upload_url($r['photo'])) ?>" target="_blank"><img src="<?= e(upload_url($r['photo'])) ?>" alt="" style="max-width:100%;border-radius:10px"></a><?php endif; ?>
  </div>
  <form class="card" method="post"><?= csrf_field() ?>
    <label style="margin-top:0">Κατάσταση</label>
    <select name="status"><?php foreach (WS_STATUS as $k => [$l]): ?><option value="<?= $k ?>" <?= $r['status'] === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
    <label>Απάντηση στον πελάτη</label><textarea name="reply" rows="5"><?= e($r['reply']) ?></textarea>
    <button class="btn dark">Αποθήκευση</button>
    <p class="hint">Ο πελάτης ειδοποιείται. Όταν ολοκληρωθεί, λαμβάνει και email.</p>
  </form>
</div>
