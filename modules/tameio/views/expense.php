<?= tm_tabs('expenses') ?>
<a class="back" href="<?= e(module_url('tameio', 'expenses')) ?>"><?= icon('back', 14) ?> Έξοδα</a>
<div class="cols">
  <div class="card">
    <div class="cardhd"><?= tile('receipt', 'teal') ?><div><b><?= money($x['amount_cents']) ?> · <?= e($x['category']) ?></b><small><?= e(date_gr($x['day'])) ?> · <?= e(TM_PAYMENT[$x['payment']]) ?><?= $x['by_name'] ? ' · ' . e($x['by_name']) : '' ?></small></div></div>
    <?php if ($x['description']): ?><p class="body mb0"><?= e($x['description']) ?></p><?php endif; ?>
    <form method="post" data-confirm="Διαγραφή του εξόδου;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή</button></form>
  </div>
  <?php if ($x['photo']): ?><a href="<?= e(upload_url($x['photo'])) ?>" target="_blank"><img src="<?= e(upload_url($x['photo'])) ?>" alt="Απόδειξη" style="width:100%;border-radius:14px;border:1px solid var(--line)"></a><?php endif; ?>
</div>
