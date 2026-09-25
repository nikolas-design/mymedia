<?= module_tabs([['overview', '', 'Το site μου']], 'overview') ?>
<a class="back" href="<?= e(module_url('websites')) ?>"><?= icon('back', 14) ?> Το site μου</a>
<div class="cols">
  <div class="card">
    <div class="cardhd"><?= tile('globe', 'blue') ?><div><b><?= e($r['title']) ?></b><small><?= e(date_gr($r['created_at'])) ?><?= $r['page_url'] ? ' · ' . e($r['page_url']) : '' ?></small></div><span style="margin-left:auto"><?= pill(...WS_STATUS[$r['status']]) ?></span></div>
    <p class="body pre"><?= e($r['description']) ?></p>
    <?php if ($r['photo']): ?><a href="<?= e(upload_url($r['photo'])) ?>" target="_blank"><img src="<?= e(upload_url($r['photo'])) ?>" alt="" style="max-width:100%;border-radius:10px;border:1px solid var(--line)"></a><?php endif; ?>
  </div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('headset', 'purple') ?><div><b>Απάντηση της ομάδας</b></div></div>
    <p class="body pre mt"><?= $r['reply'] ? e($r['reply']) : '<span class="muted">Δεν υπάρχει απάντηση ακόμα.</span>' ?></p>
  </div>
</div>
