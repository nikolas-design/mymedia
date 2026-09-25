<?= ls_tabs('today') ?>
<div class="g2 g4">
  <div class="stat"><small>Μαθήματα σήμερα</small><b><?= count($groups) ?></b></div>
  <div class="stat"><small>Ενεργοί μαθητές</small><b><?= (int) $stats['students'] ?></b></div>
  <div class="stat"><small>Τμήματα</small><b><?= (int) $stats['groups_n'] ?></b></div>
  <?php if ($canEdit): ?><a class="stat<?= $owed ? ' dark' : '' ?>" href="<?= e(module_url('lessons', 'fees')) ?>" style="color:inherit;text-decoration:none"><small>Οφειλές</small><b><?= money($owed) ?></b></a><?php endif; ?>
</div>
<p class="sechd">Σήμερα, <?= e(date_gr(date('Y-m-d'))) ?></p>
<div class="card lst" style="max-width:820px">
  <?php foreach ($groups as $g): ?>
    <a class="lr" href="<?= e(module_url('lessons', 'attendance/' . $g['id'])) ?>">
      <span style="font-weight:700;width:52px"><?= e($g['start_time'] ? substr($g['start_time'], 0, 5) : '') ?></span>
      <span class="grow"><b><?= e($g['name']) ?></b><small><?= e($g['teacher'] ?? '') ?><?= $g['room'] ? ' · ' . e($g['room']) : '' ?> · <?= (int) $g['n'] ?> μαθητές</small></span>
      <?= $g['marked'] ? pill('Παρουσίες ✓', 'green') : pill('Παρουσίες', 'orange') ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$groups): ?><div class="empty">Κανένα μάθημα σήμερα.</div><?php endif; ?>
</div>
