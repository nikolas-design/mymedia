<?= ls_tabs('groups') ?>
<div class="cols">
  <div class="card lst">
    <?php foreach ($groups as $g): ?>
      <a class="lr<?= $g['active'] ? '' : ' off' ?>" href="<?= e(module_url('lessons', 'groups/' . $g['id'])) ?>"><?= tile('book', 'indigo') ?>
        <span class="grow"><b><?= e($g['name']) ?></b><small><?= e(ls_days_label($g['weekdays'])) ?> <?= $g['start_time'] ? e(substr($g['start_time'], 0, 5)) : '' ?><?= $g['teacher'] ? ' · ' . e($g['teacher']) : '' ?> · <?= (int) $g['n'] ?><?= $g['capacity'] ? '/' . (int) $g['capacity'] : '' ?> μαθητές</small></span><?= icon('chevron') ?></a>
    <?php endforeach; ?>
    <?php if (!$groups): ?><div class="empty">Κανένα τμήμα ακόμα.</div><?php endif; ?>
  </div>
  <div><?php if ($canEdit): ?><a class="btn dark" href="<?= e(module_url('lessons', 'groups/new')) ?>"><?= icon('plus', 16) ?> Νέο τμήμα</a><?php endif; ?></div>
</div>
