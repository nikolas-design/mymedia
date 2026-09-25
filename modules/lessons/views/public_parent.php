<?php $days = [1 => 'Δευτέρα', 2 => 'Τρίτη', 3 => 'Τετάρτη', 4 => 'Πέμπτη', 5 => 'Παρασκευή', 6 => 'Σάββατο', 7 => 'Κυριακή']; ?>
<h1><?= e($s['name']) ?></h1>
<p class="lead"><?= e($bizName) ?></p>
<div class="pcard"><b>Πρόγραμμα</b><div class="list">
  <?php foreach ($groups as $g): ?><div class="it"><span><?= e($g['name']) ?><br><span class="small muted"><?= e(implode(', ', array_map(fn($d) => $days[(int) $d] ?? '', array_filter(explode(',', (string) $g['weekdays']))))) ?></span></span><b><?= $g['start_time'] ? e(substr($g['start_time'], 0, 5)) . '–' . e(substr((string) $g['end_time'], 0, 5)) : '' ?></b></div><?php endforeach; ?>
  <?php if (!$groups): ?><p class="muted">—</p><?php endif; ?>
</div></div>
<div class="pcard"><b>Τελευταίες παρουσίες</b><div class="list">
  <?php foreach ($recent as $r): ?><div class="it"><span><?= e(date_gr($r['day'])) ?> · <?= e($r['name']) ?></span><b style="color:<?= $r['present'] ? '#1e8a5a' : '#c2344d' ?>"><?= $r['present'] ? 'Παρών' : 'Απών' ?></b></div><?php endforeach; ?>
  <?php if (!$recent): ?><p class="muted">Δεν υπάρχουν ακόμα καταχωρήσεις.</p><?php endif; ?>
</div></div>
<div class="<?= $balance > 0 ? 'err' : 'ok' ?>"><?= $balance > 0 ? 'Υπόλοιπο διδάκτρων: ' . money($balance) : 'Δεν υπάρχει υπόλοιπο. Ευχαριστούμε!' ?></div>
