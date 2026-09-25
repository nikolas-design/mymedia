<?= tm_tabs('overview') ?>
<?php $expected = tm_expected($c); $diff = $c['counted_cents'] === null ? null : (int) $c['counted_cents'] - $expected; ?>
<div class="noprint" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
  <a class="back" href="<?= e(module_url('tameio')) ?>"><?= icon('back', 14) ?> Ταμείο</a>
  <button class="btn sm" type="button" data-print><?= icon('print', 14) ?> Εκτύπωση</button>
</div>
<div class="cols">
  <div class="card">
    <div class="cardhd"><?= tile('wallet', 'teal') ?><div><b>Κλείσιμο <?= e(date_gr($c['day'])) ?> · <?= e($c['shift']) ?></b><small><?= e($c['by_name'] ?? '') ?> · <?= e(date('H:i', strtotime($c['created_at']))) ?></small></div><span style="margin-left:auto"><?= tm_diff_pill($diff) ?></span></div>
    <table class="tbl"><tbody>
      <tr><td>Μετρητά</td><td class="n"><?= money($c['cash_cents']) ?></td></tr>
      <tr><td>Κάρτα / POS</td><td class="n"><?= money($c['card_cents']) ?></td></tr>
      <tr><td>Άλλο</td><td class="n"><?= money($c['other_cents']) ?></td></tr>
      <tr><td><b>Σύνολο πωλήσεων</b></td><td class="n"><b><?= money(tm_sales($c)) ?></b></td></tr>
    </tbody></table>
  </div>
  <div class="card">
    <table class="tbl"><tbody>
      <tr><td>Ρέστα έναρξης</td><td class="n"><?= money($c['opening_cents']) ?></td></tr>
      <tr><td>+ Πωλήσεις μετρητά</td><td class="n"><?= money($c['cash_cents']) ?></td></tr>
      <tr><td>− Έξοδα μετρητά</td><td class="n"><?= money($c['cash_expenses_cents']) ?></td></tr>
      <tr><td><b>Αναμενόμενα στο συρτάρι</b></td><td class="n"><b><?= tm_money($expected) ?></b></td></tr>
      <tr><td>Μετρήθηκαν</td><td class="n"><?= $c['counted_cents'] === null ? '—' : money($c['counted_cents']) ?></td></tr>
      <?php if ($diff !== null): ?><tr><td>Διαφορά</td><td class="n" style="color:<?= abs($diff) < 50 ? '#1e8a5a' : '#c2344d' ?>"><b><?= tm_money($diff) ?></b></td></tr><?php endif; ?>
    </tbody></table>
    <?php if ($c['notes']): ?><p class="small mt mb0">Σημειώσεις: <?= e($c['notes']) ?></p><?php endif; ?>
  </div>
</div>
<?php if ($expenses): ?>
  <p class="sechd">Έξοδα ημέρας</p>
  <div class="card lst"><?php foreach ($expenses as $x): ?><div class="lr"><span class="grow"><b><?= e($x['category']) ?><?= $x['description'] ? ' · ' . e($x['description']) : '' ?></b><small><?= e(TM_PAYMENT[$x['payment']]) ?></small></span><b><?= money($x['amount_cents']) ?></b></div><?php endforeach; ?></div>
<?php endif; ?>
<div class="btns noprint mt">
  <a class="btn" href="<?= e(module_url('tameio', 'close?day=' . $c['day'])) ?>">Διόρθωση</a>
  <?php if ($canEdit): ?><form method="post" data-confirm="Διαγραφή του κλεισίματος;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή</button></form><?php endif; ?>
</div>
