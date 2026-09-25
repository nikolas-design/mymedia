<?= ls_tabs('groups') ?>
<a class="back" href="<?= e(module_url('lessons', 'groups')) ?>"><?= icon('back', 14) ?> Τμήματα</a>
<div class="cols">
<form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="save">
  <label style="margin-top:0">Όνομα τμήματος</label><input name="name" value="<?= e($g['name'] ?? '') ?>" required placeholder="π.χ. Αγγλικά B1 · Παιδικό">
  <div class="row2"><div><label>Καθηγητής</label><input name="teacher" value="<?= e($g['teacher'] ?? '') ?>"></div><div><label>Αίθουσα</label><input name="room" value="<?= e($g['room'] ?? '') ?>"></div></div>
  <label>Ημέρες</label>
  <div class="btns"><?php foreach (LS_DAYS as $d => $l): ?><label class="btn sm" style="font-weight:500;gap:4px"><input type="checkbox" name="weekdays[]" value="<?= $d ?>" <?= in_array((string) $d, explode(',', (string) ($g['weekdays'] ?? '')), true) ? 'checked' : '' ?>> <?= $l ?></label><?php endforeach; ?></div>
  <div class="row2"><div><label>Από</label><input type="time" name="start_time" value="<?= e(substr((string) ($g['start_time'] ?? ''), 0, 5)) ?>"></div><div><label>Έως</label><input type="time" name="end_time" value="<?= e(substr((string) ($g['end_time'] ?? ''), 0, 5)) ?>"></div></div>
  <div class="row2"><div><label>Μέγ. μαθητές</label><input type="number" name="capacity" value="<?= e($g['capacity'] ?? '') ?>"></div><div><label>Δίδακτρα / μήνα (€)</label><input name="fee" value="<?= isset($g['monthly_fee_cents']) ? e(number_format($g['monthly_fee_cents'] / 100, 2, ',', '')) : '' ?>" inputmode="decimal"></div></div>
  <?php if ($g): ?><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $g['active'] ? 'checked' : '' ?>> Ενεργό</label><?php endif; ?>
  <?php if ($canEdit): ?><button class="btn dark"><?= $isNew ? 'Δημιουργία' : 'Αποθήκευση' ?></button><?php endif; ?>
</form>
<?php if ($g): ?>
<div>
  <p class="sechd" style="margin-top:0">Μαθητές (<?= count($students) ?><?= $g['capacity'] ? '/' . (int) $g['capacity'] : '' ?>)</p>
  <div class="card lst">
    <?php foreach ($students as $s): ?>
      <div class="lr"><a class="grow" style="color:inherit" href="<?= e(module_url('lessons', 'students/' . $s['id'])) ?>"><b><?= e($s['name']) ?></b><small><?= $s['att'] !== null ? 'παρουσίες ' . (int) $s['att'] . '%' : 'χωρίς παρουσίες' ?><?= $s['phone'] ? ' · ' . e($s['phone']) : '' ?></small></a>
        <?php if ($canEdit): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="unenroll"><input type="hidden" name="student_id" value="<?= (int) $s['id'] ?>"><button class="btn sm danger" title="Αφαίρεση">✕</button></form><?php endif; ?></div>
    <?php endforeach; ?>
  </div>
  <?php if ($canEdit && $others): ?>
    <form method="post" class="inline"><?= csrf_field() ?><input type="hidden" name="action" value="enroll">
      <select name="student_id"><?php foreach ($others as $o): ?><option value="<?= (int) $o['id'] ?>"><?= e($o['name']) ?></option><?php endforeach; ?></select><button class="btn sm dark auto" style="height:44px">Εγγραφή</button></form>
  <?php endif; ?>
  <a class="btn" href="<?= e(module_url('lessons', 'attendance/' . $g['id'])) ?>">Παρουσίες</a>
  <?php if ($canEdit): ?><form method="post" data-confirm="Διαγραφή του τμήματος και των παρουσιών του;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή τμήματος</button></form><?php endif; ?>
</div>
<?php endif; ?>
</div>
