<span class="eyebrow"><?= icon('bell', 12) ?> Ειδοποιήσεις</span>
<h1>Τα νέα <em>σου.</em></h1>
<div class="card lst" style="max-width:720px">
  <?php foreach ($items as $n): ?>
    <a class="lr" href="<?= e($n['link'] ? url($n['link']) : '#') ?>">
      <?= tile('bell', $n['read_at'] ? 'grey' : 'purple') ?>
      <span class="grow"><b><?= e($n['title']) ?></b><small><?= e(ago($n['created_at'])) ?></small></span>
      <?php if (!$n['read_at']): ?><?= pill('Νέο', 'purple') ?><?php endif; ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$items): ?><div class="empty">Δεν υπάρχουν ειδοποιήσεις.</div><?php endif; ?>
</div>
