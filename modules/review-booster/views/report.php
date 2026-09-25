<?= rb_tabs('report') ?>
<?php
$delta = function ($a, $b, bool $pct = false) {
    if ($a === null || $b === null) return '';
    $d = $a - $b;
    if (abs($d) < 0.05) return '<span class="small muted">=</span>';
    return '<span class="small" style="color:' . ($d > 0 ? '#1e8a5a' : '#c2344d') . '">' . ($d > 0 ? '▲ ' : '▼ ') . str_replace('.', ',', (string) abs(round($d, 1))) . ($pct ? '%' : '') . '</span>';
};
?>
<div class="noprint" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
  <p class="sechd" style="margin:0">Εβδομάδα <?= e(date_gr($thisFrom, false)) ?> – <?= e(date_gr(date('Y-m-d'), false)) ?></p>
  <button class="btn sm" type="button" data-print><?= icon('print', 14) ?> Εκτύπωση / PDF</button>
</div>
<div class="g2 g4">
  <div class="stat"><small>Αξιολογήσεις</small><b><?= $now['count'] ?></b> <?= $delta($now['count'], $prev['count']) ?></div>
  <div class="stat"><small>Μέσος όρος</small><b><?= $now['avg'] !== null ? number_format($now['avg'], 1, ',', '') : '—' ?></b> <?= $delta($now['avg'], $prev['avg']) ?></div>
  <div class="stat"><small>Θετικές</small><b><?= $now['positive'] !== null ? $now['positive'] . '%' : '—' ?></b> <?= $delta($now['positive'], $prev['positive'], true) ?></div>
  <div class="stat"><small>Στο Google</small><b><?= $now['google'] ?></b> <?= $delta($now['google'], $prev['google']) ?></div>
</div>
<div class="cols mt">
  <div class="card scrollx">
    <div class="cardhd"><?= tile('building', 'blue') ?><div><b>Ανά σημείο</b></div></div>
    <table class="tbl"><thead><tr><th>Σημείο</th><th class="n">Αξιολ.</th><th class="n">Μ.Ο.</th><th class="n">Google</th></tr></thead><tbody>
      <?php foreach ($byLocation as $r): ?><tr><td><?= e($r['name']) ?></td><td class="n"><?= (int) $r['n'] ?></td><td class="n"><?= $r['avg'] ? number_format((float) $r['avg'], 1, ',', '') : '—' ?></td><td class="n"><?= (int) $r['google'] ?></td></tr><?php endforeach; ?>
    </tbody></table>
  </div>
  <div class="card">
    <div class="cardhd"><?= tile('headset', 'pink') ?><div><b>Ιδιωτικά σχόλια της εβδομάδας</b><small><?= count($feedback) ?> σχόλια</small></div></div>
    <?php foreach ($feedback as $f): ?><div class="step" style="display:block"><?= rb_stars((int) $f['stars']) ?> <span class="small"><?= e($f['message']) ?></span></div><?php endforeach; ?>
    <?php if (!$feedback): ?><div class="empty">Κανένα αρνητικό σχόλιο αυτή την εβδομάδα.</div><?php endif; ?>
  </div>
</div>
