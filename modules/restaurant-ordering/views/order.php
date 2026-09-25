<?= ro_tabs('orders') ?>
<div class="noprint" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
  <a class="back" href="<?= e(module_url('restaurant-ordering')) ?>"><?= icon('back', 14) ?> Παραγγελίες</a>
  <button class="btn sm" type="button" data-print><?= icon('print', 14) ?> Εκτύπωση για κουζίνα</button>
</div>
<div class="cols">
  <div class="card inv" style="max-width:420px">
    <div style="text-align:center"><b style="font-size:22px">#<?= (int) $o['number'] ?> · <?= e(RO_KIND[$o['kind']]) ?></b><br><span class="small"><?= e(date_gr($o['created_at'])) ?> <?= e(date('H:i', strtotime($o['created_at']))) ?></span></div>
    <table><tbody>
      <?php foreach ($lines as $l): ?><tr><td><b><?= (int) $l['qty'] ?>×</b> <?= e($l['name']) ?><?= $l['note'] ? '<br><i class="small">' . e($l['note']) . '</i>' : '' ?></td><td class="n"><?= money($l['qty'] * $l['price_cents']) ?></td></tr><?php endforeach; ?>
      <?php if ($o['fee_cents']): ?><tr><td>Κόστος αποστολής</td><td class="n"><?= money($o['fee_cents']) ?></td></tr><?php endif; ?>
      <tr class="tot"><td>Σύνολο · <?= $o['payment'] === 'card' ? 'Κάρτα' : 'Μετρητά' ?></td><td class="n"><?= money($o['total_cents']) ?></td></tr>
    </tbody></table>
    <p class="small"><b><?= e($o['name']) ?></b> · <?= e($o['phone']) ?><?php if ($o['kind'] === 'delivery'): ?><br><?= e($o['address']) ?><?= $o['floor_bell'] ? ' · ' . e($o['floor_bell']) : '' ?><?php endif; ?><?= $o['notes'] ? '<br>Σημ.: ' . e($o['notes']) : '' ?></p>
  </div>
  <div class="noprint">
    <div class="card"><div class="cardhd" style="margin-bottom:0"><?= tile('utensils', 'orange') ?><div><b><?= e(RO_STATUS[$o['status']][0]) ?></b><small><?= $o['eta_minutes'] ? 'Χρόνος: ' . (int) $o['eta_minutes'] . ' λεπτά' : '' ?></small></div></div>
      <?php if ($o['status'] === 'new'): ?>
        <form method="post" class="mt"><?= csrf_field() ?><label style="margin-top:0">Απόρριψη (π.χ. εκτός περιοχής)</label><input name="reason" placeholder="Λόγος"><button class="btn danger" name="do" value="reject">Απόρριψη παραγγελίας</button></form>
      <?php endif; ?>
    </div>
  </div>
</div>
