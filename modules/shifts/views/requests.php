<?= sh_tabs('requests') ?>
<div class="cols">
<div class="card lst">
  <?php foreach ($requests as $r): ?>
    <div class="lr" style="flex-wrap:wrap">
      <span class="grow" style="min-width:200px"><b><?= e($r['name']) ?> · <?= e(SH_KIND[$r['kind']]) ?></b>
        <small><?= e(date_gr($r['day_from'], false)) ?><?= $r['day_to'] !== $r['day_from'] ? ' – ' . e(date_gr($r['day_to'], false)) : '' ?><?= $r['note'] ? ' · ' . e($r['note']) : '' ?></small></span>
      <?php if ($r['status'] === 'new' && $canEdit): ?>
        <form method="post" class="btns"><?= csrf_field() ?><input type="hidden" name="action" value="decide"><input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
          <button class="btn sm dark" name="status" value="approved">Έγκριση</button><button class="btn sm" name="status" value="rejected">Όχι</button></form>
      <?php else: ?>
        <?= match ($r['status']) { 'approved' => pill('Εγκρίθηκε', 'green'), 'rejected' => pill('Απορρίφθηκε', 'grey'), default => pill('Σε αναμονή', 'orange') } ?>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$requests): ?><div class="empty">Κανένα αίτημα.</div><?php endif; ?>
</div>
<?php if ($canEdit || $me): ?>
<form class="card" method="post">
  <?= csrf_field() ?>
  <div class="cardhd" style="margin-bottom:0"><?= tile('calendar', 'orange') ?><div><b><?= $canEdit ? 'Καταχώρηση ρεπό / άδειας' : 'Νέο αίτημα' ?></b></div></div>
  <?php if ($canEdit): ?><label>Άτομο</label><select name="person_id"><?php foreach ($people as $p): ?><option value="<?= (int) $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select><?php endif; ?>
  <label>Τι</label><select name="kind"><?php foreach (SH_KIND as $k => $v): ?><option value="<?= $k ?>"><?= e($v) ?></option><?php endforeach; ?></select>
  <div class="row2">
    <div><label>Από</label><input type="date" name="day_from" required></div>
    <div><label>Έως</label><input type="date" name="day_to"></div>
  </div>
  <label>Σημείωση</label><input name="note" placeholder="π.χ. αλλάζω με τη Μαρία">
  <button class="btn dark"><?= $canEdit ? 'Καταχώρηση' : 'Αποστολή' ?></button>
</form>
<?php endif; ?>
</div>
