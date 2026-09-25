<?php
$gm = ['01'=>'Ιαν','02'=>'Φεβ','03'=>'Μαρ','04'=>'Απρ','05'=>'Μάι','06'=>'Ιούν','07'=>'Ιούλ','08'=>'Αύγ','09'=>'Σεπ','10'=>'Οκτ','11'=>'Νοέ','12'=>'Δεκ'];
$maxTool = $byTool ? max($byTool) : 1;
$maxMonth = max(1, max($months));
?>
<span class="eyebrow"><?= icon('shield', 12) ?> Διαχείριση πλατφόρμας</span>
<h1>Επισκόπηση</h1>

<div class="g2 g4">
  <div class="stat dark"><small>MRR</small><b><?= money($mrr) ?></b></div>
  <a class="stat" href="<?= e(url('admin/businesses')) ?>" style="color:inherit;text-decoration:none"><small>Επιχειρήσεις</small><b><?= $businessCount ?></b></a>
  <a class="stat" href="<?= e(url('admin/requests')) ?>" style="color:inherit;text-decoration:none"><small>Ανοιχτά αιτήματα</small><b><?= count($requests) ?></b></a>
  <a class="stat" href="<?= e(url('admin/invoices?status=issued')) ?>" style="color:inherit;text-decoration:none"><small>Ανείσπρακτα</small><b><?= money($unpaid) ?></b></a>
</div>

<div class="cols mt">
<div>
  <p class="sechd" style="margin-top:0">Αιτήματα ενεργοποίησης</p>
  <div class="card lst">
    <?php foreach ($requests as $r): ?>
      <div class="lr">
        <span class="grow"><b><?= e($r['business_name']) ?> → <?= e($r['tool_name']) ?></b>
          <small><?= $r['plan_name'] ? e($r['plan_name']) . ' · ' . money($r['price_cents']) . period_label($r['period']) : 'Ενδιαφέρον' ?><?= $r['billing'] === 'year' && $r['period'] === 'month' ? ' · ετήσια' : '' ?> · <?= e(ago($r['created_at'])) ?><?= $r['note'] ? ' · «' . e($r['note']) . '»' : '' ?></small></span>
        <?php if ($r['plan_id']): ?>
          <form method="post" action="<?= e(url('admin/requests')) ?>"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><input type="hidden" name="do" value="activate"><button class="btn sm dark" type="submit">Ενεργοποίηση</button></form>
        <?php else: ?><?= request_pill($r['status']) ?><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (!$requests): ?><div class="empty">Κανένα ανοιχτό αίτημα.</div><?php endif; ?>
  </div>

  <p class="sechd">Ανοιχτά tickets</p>
  <div class="card lst">
    <?php foreach ($tickets as $t): ?>
      <a class="lr" href="<?= e(url('admin/tickets/' . $t['id'])) ?>">
        <span class="grow"><b><?= e($t['subject']) ?></b><small><?= e($t['business_name']) ?> · <?= e(ago($t['updated_at'])) ?></small></span>
        <?= ticket_pill($t['status']) ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$tickets): ?><div class="empty">Κανένα ανοιχτό ticket.</div><?php endif; ?>
  </div>
</div>

<div>
  <div class="card">
    <div class="cardhd"><?= tile('chart', 'purple') ?><div><b>MRR ανά εργαλείο</b><small><?= e(date('m/Y')) ?></small></div></div>
    <?php foreach ($byTool as $name => $c): ?>
      <div class="hb"><span><?= e($name) ?></span><i><b style="width:<?= round($c / $maxTool * 100) ?>%"></b></i><em><?= money($c) ?></em></div>
    <?php endforeach; ?>
    <?php if (!$byTool): ?><div class="empty">Καμία ενεργή συνδρομή.</div><?php endif; ?>
  </div>
  <div class="card">
    <div class="cardhd"><?= tile('building', 'blue') ?><div><b>Νέες επιχειρήσεις / μήνα</b><small>Τελευταίοι 6 μήνες</small></div></div>
    <div class="bars"><?php foreach ($months as $n): ?><i style="height:<?= round($n / $maxMonth * 100) ?>%" title="<?= $n ?>"></i><?php endforeach; ?></div>
    <div class="barlbl"><?php foreach ($months as $ym => $n): ?><span><?= $gm[substr($ym, 5)] ?> · <?= $n ?></span><?php endforeach; ?></div>
  </div>
</div>
</div>
