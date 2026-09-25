<?= of_tabs('overview') ?>
<div class="g2 g4">
  <div class="stat"><small>Αγορές μήνα (εκτίμηση)</small><b><?= money($month) ?></b></div>
  <div class="stat"><small>Προηγούμενος μήνας</small><b><?= money($prevMonth) ?></b></div>
  <div class="stat"><small>Αναμένονται</small><b><?= count($pending) ?></b></div>
  <div class="stat"><small>Προμηθευτές</small><b><?= count($suppliers) ?></b></div>
</div>
<div class="cols mt">
<div>
  <p class="sechd" style="margin-top:0">Παραγγελία σε…</p>
  <div class="card lst">
    <?php foreach ($suppliers as $s): ?>
      <a class="lr" href="<?= e(module_url('orderflow', 'new/' . $s['id'])) ?>">
        <span class="av" style="<?= avatar_style($s['name']) ?>"><?= e(initials($s['name'])) ?></span>
        <span class="grow"><b><?= e($s['name']) ?></b><small><?= (int) $s['products'] ?> είδη<?= $s['order_days'] ? ' · ' . e($s['order_days']) : '' ?><?= $s['last_order'] ? ' · τελευταία ' . e(ago($s['last_order'])) : '' ?></small></span>
        <?= icon('chevron') ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$suppliers): ?><div class="empty">Πρόσθεσε πρώτα προμηθευτές και τα είδη τους. <a href="<?= e(module_url('orderflow', 'suppliers/new')) ?>">Νέος προμηθευτής</a></div><?php endif; ?>
  </div>
</div>
<div>
  <?php if ($drafts): ?>
    <p class="sechd" style="margin-top:0">Πρόχειρες</p>
    <div class="card lst">
      <?php foreach ($drafts as $o): ?>
        <a class="lr" href="<?= e(module_url('orderflow', 'orders/' . $o['id'])) ?>"><span class="grow"><b><?= e($o['supplier']) ?></b><small>ξεκίνησε <?= e(ago($o['created_at'])) ?></small></span><?= pill('Πρόχειρη', 'grey') ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
  <p class="sechd"<?= $drafts ? '' : ' style="margin-top:0"' ?>>Αναμένονται</p>
  <div class="card lst">
    <?php foreach ($pending as $o): ?>
      <a class="lr" href="<?= e(module_url('orderflow', 'orders/' . $o['id'])) ?>"><span class="grow"><b><?= e($o['supplier']) ?></b><small><?= $o['delivery_date'] ? 'παράδοση ' . e(date_gr($o['delivery_date'], false)) : 'στάλθηκε ' . e(ago($o['sent_at'])) ?><?= $o['total_cents'] ? ' · ' . money($o['total_cents']) : '' ?></small></span><?= pill('Στάλθηκε', 'purple') ?></a>
    <?php endforeach; ?>
    <?php if (!$pending): ?><div class="empty">Καμία παραγγελία σε αναμονή.</div><?php endif; ?>
  </div>
</div>
</div>
