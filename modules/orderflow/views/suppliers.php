<?= of_tabs('suppliers') ?>
<div class="cols">
<div class="card lst">
  <?php foreach ($suppliers as $s): ?>
    <a class="lr<?= $s['active'] ? '' : ' off' ?>" href="<?= e(module_url('orderflow', 'suppliers/' . $s['id'])) ?>">
      <span class="av" style="<?= avatar_style($s['name']) ?>"><?= e(initials($s['name'])) ?></span>
      <span class="grow"><b><?= e($s['name']) ?></b><small><?= (int) $s['products'] ?> είδη<?= $s['phone'] ? ' · ' . e($s['phone']) : '' ?><?= $s['email'] ? ' · ' . e($s['email']) : '' ?></small></span>
      <?= icon('chevron') ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$suppliers): ?><div class="empty">Κανένας προμηθευτής ακόμα.</div><?php endif; ?>
</div>
<div>
  <?php if ($canEdit): ?><a class="btn dark" href="<?= e(module_url('orderflow', 'suppliers/new')) ?>"><?= icon('plus', 16) ?> Νέος προμηθευτής</a><?php endif; ?>
</div>
</div>
