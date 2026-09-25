<h1>Υποστήριξη</h1>
<div class="btns" style="margin-bottom:14px">
  <?php foreach (['open' => 'Ανοιχτά', 'answered' => 'Απαντημένα', 'closed' => 'Κλειστά', 'all' => 'Όλα'] as $k => $v): ?>
    <a class="btn sm<?= $status === $k ? ' dark' : '' ?>" href="<?= e(url('admin/tickets?status=' . $k)) ?>"><?= e($v) ?></a>
  <?php endforeach; ?>
</div>
<div class="card lst" style="max-width:820px">
  <?php foreach ($tickets as $t): ?>
    <a class="lr" href="<?= e(url('admin/tickets/' . $t['id'])) ?>">
      <?= tile('headset', $t['status'] === 'open' ? 'pink' : 'grey') ?>
      <span class="grow"><b><?= e($t['subject']) ?></b><small><?= e($t['business_name']) ?> · <?= (int) $t['n'] ?> μηνύματα · <?= e(ago($t['updated_at'])) ?></small></span>
      <?= ticket_pill($t['status']) ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$tickets): ?><div class="empty">Κανένα ticket.</div><?php endif; ?>
</div>
