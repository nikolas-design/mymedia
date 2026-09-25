<?= ls_tabs('students') ?>
<a class="back" href="<?= e(module_url('lessons', 'students')) ?>"><?= icon('back', 14) ?> Μαθητές</a>
<div class="cols">
<form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save">
  <label style="margin-top:0">Ονοματεπώνυμο μαθητή</label><input name="name" value="<?= e($s['name'] ?? '') ?>" required>
  <div class="row2"><div><label>Γονέας / κηδεμόνας</label><input name="parent_name" value="<?= e($s['parent_name'] ?? '') ?>"></div><div><label>Τηλέφωνο</label><input name="phone" value="<?= e($s['phone'] ?? '') ?>"></div></div>
  <label>Email</label><input name="email" type="email" value="<?= e($s['email'] ?? '') ?>">
  <label>Σημειώσεις</label><input name="notes" value="<?= e($s['notes'] ?? '') ?>">
  <?php if ($isNew && $groups): ?><label>Εγγραφή σε τμήμα</label><select name="group_id"><option value="">—</option><?php foreach ($groups as $g): ?><option value="<?= (int) $g['id'] ?>"><?= e($g['name']) ?></option><?php endforeach; ?></select><?php endif; ?>
  <?php if ($s): ?><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $s['active'] ? 'checked' : '' ?>> Ενεργός</label><?php endif; ?>
  <?php if ($canEdit): ?><button class="btn dark"><?= $isNew ? 'Προσθήκη' : 'Αποθήκευση' ?></button><?php endif; ?>
</form>
<?php if ($s): ?>
<div>
  <div class="card lst">
    <?php foreach ($mine as $g): ?><a class="lr" href="<?= e(module_url('lessons', 'groups/' . $g['id'])) ?>"><?= tile('book', 'indigo') ?><span class="grow"><b><?= e($g['name']) ?></b><small><?= e(ls_days_label($g['weekdays'])) ?> <?= $g['start_time'] ? e(substr($g['start_time'], 0, 5)) : '' ?> · παρουσίες <?= $g['att'] !== null ? (int) $g['att'] . '%' : '—' ?></small></span></a><?php endforeach; ?>
    <?php if (!$mine): ?><div class="empty">Δεν είναι γραμμένος σε τμήμα.</div><?php endif; ?>
  </div>
  <?php if ($canEdit): $bal = ls_balance((int) $s['id']); ?>
    <div class="card">
      <div class="cardhd"><?= tile('receipt', 'orange') ?><div><b>Δίδακτρα</b><small>Υπόλοιπο: <?= money($bal) ?></small></div></div>
      <?php foreach ($charges as $c): ?><div class="step"><span class="small"><?= e($c['month']) ?> · <?= e($c['description']) ?></span><span class="small"><?= money($c['amount_cents']) ?> <?= $c['paid_cents'] >= $c['amount_cents'] ? pill('Εξοφλήθηκε', 'green') : pill('Οφείλει ' . money($c['amount_cents'] - $c['paid_cents']), 'orange') ?></span></div><?php endforeach; ?>
      <form method="post" class="inline mt"><?= csrf_field() ?><input type="hidden" name="action" value="pay"><input name="amount" inputmode="decimal" placeholder="Ποσό €" value="<?= $bal > 0 ? e(number_format($bal / 100, 2, ',', '')) : '' ?>"><button class="btn sm dark auto" style="height:44px">Πληρωμή</button></form>
      <form method="post" class="inline mt"><?= csrf_field() ?><input type="hidden" name="action" value="charge"><input name="description" placeholder="Έκτακτη χρέωση (π.χ. βιβλία)"><input name="amount" inputmode="decimal" placeholder="€" style="width:90px"><button class="btn sm auto" style="height:44px">+</button></form>
    </div>
    <div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('users', 'purple') ?><div><b>Σύνδεσμος για τον γονέα</b><small>Βλέπει πρόγραμμα, παρουσίες και υπόλοιπο, χωρίς λογαριασμό.</small></div></div>
      <div class="copy"><input id="plink" value="<?= e(full_url('p/lessons/' . $s['token'])) ?>" readonly><button class="btn sm" type="button" data-copy="#plink">Αντιγραφή</button></div></div>
  <?php endif; ?>
</div>
<?php endif; ?>
</div>
