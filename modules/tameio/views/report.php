<?= tm_tabs('report') ?>
<?php
$tot = ['cash' => 0, 'card' => 0, 'other' => 0, 'exp' => 0, 'diff' => 0];
foreach ($days as $d) { foreach ($tot as $k => $_) { $tot[$k] += (int) $d[$k]; } }
$sales = $tot['cash'] + $tot['card'] + $tot['other'];
?>
<div class="noprint" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:12px;gap:8px">
  <form method="get"><input type="month" name="m" value="<?= e($month) ?>" onchange="this.form.submit()" style="max-width:200px"></form>
  <button class="btn sm" type="button" data-print><?= icon('print', 14) ?> Εκτύπωση / PDF</button>
</div>
<div class="g2 g4">
  <div class="stat dark"><small>Πωλήσεις</small><b><?= money($sales) ?></b></div>
  <div class="stat"><small>Έξοδα</small><b><?= money($tot['exp']) ?></b></div>
  <div class="stat"><small>Καθαρό (πωλήσεις − έξοδα)</small><b><?= tm_money($sales - $tot['exp']) ?></b></div>
  <div class="stat"><small>Διαφορές ταμείου</small><b style="color:<?= abs($tot['diff']) < 50 ? 'inherit' : '#c2344d' ?>"><?= tm_money($tot['diff']) ?></b></div>
</div>
<div class="cols mt">
  <div class="card scrollx">
    <table class="tbl">
      <thead><tr><th>Ημέρα</th><th class="n">Μετρητά</th><th class="n">Κάρτα</th><th class="n">Άλλο</th><th class="n">Έξοδα</th><th class="n">Διαφ.</th></tr></thead>
      <tbody>
        <?php foreach ($days as $d => $r): ?>
          <tr><td><?= e(date_gr($d, false)) ?></td><td class="n"><?= money($r['cash']) ?></td><td class="n"><?= money($r['card']) ?></td><td class="n"><?= money($r['other']) ?></td><td class="n"><?= money($r['exp']) ?></td>
            <td class="n" style="color:<?= abs((int) $r['diff']) < 50 ? 'inherit' : '#c2344d' ?>"><?= $r['counted'] ? tm_money((int) $r['diff']) : '—' ?></td></tr>
        <?php endforeach; ?>
        <?php if (!$days): ?><tr><td colspan="6" class="empty">Καμία κίνηση αυτόν τον μήνα.</td></tr><?php else: ?>
          <tr><td><b>Σύνολο</b></td><td class="n"><b><?= money($tot['cash']) ?></b></td><td class="n"><b><?= money($tot['card']) ?></b></td><td class="n"><b><?= money($tot['other']) ?></b></td><td class="n"><b><?= money($tot['exp']) ?></b></td><td class="n"><b><?= tm_money($tot['diff']) ?></b></td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
  <div class="card">
    <div class="cardhd"><?= tile('chart', 'teal') ?><div><b>Έξοδα ανά κατηγορία</b></div></div>
    <?php $max = max(1, ...array_map(fn($r) => (int) $r['total'], $byCat ?: [['total' => 1]])); foreach ($byCat as $r): ?>
      <div class="hb"><span><?= e($r['category']) ?></span><i><b style="width:<?= round($r['total'] / $max * 100) ?>%;background:#1d9e75"></b></i><em><?= money($r['total']) ?></em></div>
    <?php endforeach; ?>
    <?php if (!$byCat): ?><div class="empty">Κανένα έξοδο.</div><?php endif; ?>
  </div>
</div>
