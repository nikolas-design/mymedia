<?= ls_tabs('today') ?>
<a class="back" href="<?= e(module_url('lessons', 'groups/' . $g['id'])) ?>"><?= icon('back', 14) ?> <?= e($g['name']) ?></a>
<form method="post" class="card" style="max-width:640px"><?= csrf_field() ?>
  <div class="inline"><input type="date" name="d" value="<?= e($day) ?>" onchange="location='?d='+this.value" style="max-width:200px"><span class="small muted"><?= $marks ? 'Έχουν καταχωρηθεί' : 'Όλοι σημειωμένοι παρόντες· ξετσέκαρε τους απόντες' ?></span></div>
  <?php foreach ($students as $s): $p = $marks[(int) $s['id']] ?? 1; ?>
    <label class="step mt" style="cursor:pointer;font-weight:500"><span><?= e($s['name']) ?></span><input type="checkbox" name="present[]" value="<?= (int) $s['id'] ?>" <?= $p ? 'checked' : '' ?> style="width:22px;height:22px"></label>
  <?php endforeach; ?>
  <?php if (!$students): ?><div class="empty">Το τμήμα δεν έχει μαθητές.</div><?php else: ?><button class="btn dark">Αποθήκευση παρουσιών</button><?php endif; ?>
</form>
