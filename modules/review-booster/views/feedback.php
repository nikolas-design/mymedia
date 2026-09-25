<?= rb_tabs('feedback') ?>
<div class="btns" style="margin-bottom:14px">
  <?php foreach (['new' => 'Νέα', 'resolved' => 'Λυμένα', 'all' => 'Όλα'] as $k => $v): ?>
    <a class="btn sm<?= $status === $k ? ' dark' : '' ?>" href="<?= e(module_url('review-booster', 'feedback?status=' . $k)) ?>"><?= e($v) ?></a>
  <?php endforeach; ?>
</div>
<div class="card lst" style="max-width:820px">
  <?php foreach ($items as $f): ?>
    <a class="lr" href="<?= e(module_url('review-booster', 'feedback/' . $f['id'])) ?>">
      <span class="grow"><b><?= rb_stars((int) $f['stars']) ?> <?= e($f['name'] ?: 'Ανώνυμα') ?><?= $f['contact'] ? ' · ' . e($f['contact']) : '' ?></b>
        <small><?= e($f['location']) ?> · <?= e(ago($f['created_at'])) ?> · <?= e($f['message']) ?></small></span>
      <?= $f['status'] === 'new' ? pill('Νέο', 'orange') : pill('Λύθηκε', 'green') ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$items): ?><div class="empty">Κανένα σχόλιο εδώ.</div><?php endif; ?>
</div>
