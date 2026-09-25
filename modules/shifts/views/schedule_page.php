<!doctype html>
<html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta name="robots" content="noindex">
<title>Πρόγραμμα · <?= e($title) ?></title>
<link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
<style>body{background:#fff}.wrap{max-width:1100px}table.sched{width:100%;border-collapse:collapse;font-size:13px}.sched th,.sched td{border:1px solid var(--line);padding:6px;vertical-align:top;text-align:left}.sched th{background:#fafafe}.sh{font-weight:600;color:var(--p)}.today{background:#faf7ff}@media print{.noprint{display:none}@page{size:A4 landscape;margin:10mm}}</style>
</head><body><div class="wrap">
<?php $days = sh_week_days($monday); ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap">
  <div><h1 style="font-size:22px;margin:0"><?= e($title) ?> · Πρόγραμμα</h1><p class="small muted"><?= e(date_gr($monday, false)) ?> – <?= e(date_gr($days[6])) ?></p></div>
  <div class="btns noprint">
    <?php if (!empty($nav)): ?><a class="btn sm" href="<?= e($nav . '?w=' . date('Y-m-d', strtotime("$monday -7 days"))) ?>">‹ Προηγούμενη</a><a class="btn sm" href="<?= e($nav . '?w=' . date('Y-m-d', strtotime("$monday +7 days"))) ?>">Επόμενη ›</a><?php endif; ?>
    <button class="btn sm dark" onclick="print()">Εκτύπωση</button>
  </div>
</div>
<div class="scrollx">
<table class="sched"><thead><tr><th></th><?php foreach ($days as $i => $d): ?><th class="<?= $d === date('Y-m-d') ? 'today' : '' ?>"><?= e(SH_DAYS[$i]) ?><br><span class="muted"><?= e(date('d/m', strtotime($d))) ?></span></th><?php endforeach; ?></tr></thead>
<tbody><?php foreach ($people as $p): ?><tr><td><b><?= e($p['name']) ?></b><br><span class="muted"><?= e($p['position'] ?? '') ?></span></td>
  <?php foreach ($days as $d): ?><td class="<?= $d === date('Y-m-d') ? 'today' : '' ?>">
    <?php if (isset($abs[(int) $p['id']][$d])): ?><span class="muted"><?= e(SH_KIND[$abs[(int) $p['id']][$d]]) ?></span><?php endif; ?>
    <?php foreach ($grid[(int) $p['id']][$d] ?? [] as $s): ?><div class="sh"><?= e(sh_time($s['start_time'])) ?>–<?= e(sh_time($s['end_time'])) ?></div><?php endforeach; ?>
  </td><?php endforeach; ?></tr><?php endforeach; ?></tbody></table>
</div>
<?php if (!empty($unpublished)): ?><p class="small muted">Το πρόγραμμα αυτής της εβδομάδας δεν έχει δημοσιευτεί ακόμα.</p><?php endif; ?>
</div></body></html>
