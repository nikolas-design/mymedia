<?= ls_tabs('students') ?>
<div style="display:flex;gap:8px;justify-content:space-between;flex-wrap:wrap;margin-bottom:12px">
  <form method="get" class="inline" style="max-width:420px"><input name="q" value="<?= e($q) ?>" placeholder="Όνομα, γονέας ή τηλέφωνο"><button class="btn sm auto" style="height:44px">Αναζήτηση</button></form>
  <?php if ($canEdit): ?><a class="btn dark auto" href="<?= e(module_url('lessons', 'students/new')) ?>" style="margin:0"><?= icon('plus', 16) ?> Νέος μαθητής</a><?php endif; ?>
</div>
<div class="card lst" style="max-width:900px">
  <?php foreach ($students as $s): ?>
    <a class="lr<?= $s['active'] ? '' : ' off' ?>" href="<?= e(module_url('lessons', 'students/' . $s['id'])) ?>">
      <span class="av" style="<?= avatar_style($s['name']) ?>"><?= e(initials($s['name'])) ?></span>
      <span class="grow"><b><?= e($s['name']) ?></b><small><?= e($s['groups_list'] ?: 'χωρίς τμήμα') ?><?= $s['parent_name'] ? ' · ' . e($s['parent_name']) : '' ?></small></span>
      <?php if ($canEdit && $s['balance'] > 0): ?><?= pill('Οφειλή ' . money($s['balance']), 'orange') ?><?php endif; ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$students): ?><div class="empty">Κανένας μαθητής.</div><?php endif; ?>
</div>
