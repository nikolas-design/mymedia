<h1>Παραστατικά</h1>
<div class="g2" style="max-width:520px">
  <div class="stat"><small>Ανείσπρακτα</small><b><?= money($totals['unpaid']) ?></b></div>
  <div class="stat"><small>Εισπράξεις μήνα</small><b><?= money($totals['paid_month']) ?></b></div>
</div>
<div class="btns mt" style="margin-bottom:14px">
  <?php foreach (['' => 'Όλα', 'issued' => 'Ανείσπρακτα', 'paid' => 'Πληρωμένα', 'void' => 'Ακυρωμένα'] as $k => $v): ?>
    <a class="btn sm<?= $status === $k ? ' dark' : '' ?>" href="<?= e(url('admin/invoices' . ($k ? '?status=' . $k : ''))) ?>"><?= e($v) ?></a>
  <?php endforeach; ?>
</div>
<p class="small muted">Νέο παραστατικό εκδίδεις από τη σελίδα της επιχείρησης.</p>
<div class="card scrollx">
  <table class="tbl">
    <thead><tr><th>Αριθμός</th><th>Επιχείρηση</th><th>Έκδοση</th><th class="n">Σύνολο</th><th>Κατάσταση</th><th></th></tr></thead>
    <tbody>
    <?php foreach ($invoices as $i): ?>
      <tr>
        <td><a href="<?= e(url('invoices/' . $i['id'])) ?>"><?= e($i['number']) ?></a></td>
        <td><a href="<?= e(url('admin/businesses/' . $i['business_id'])) ?>" style="color:inherit"><?= e($i['business_name']) ?></a></td>
        <td><?= e(date_gr($i['issued_on'])) ?></td>
        <td class="n"><?= money($i['total_cents']) ?></td>
        <td><?= invoice_pill($i['status'], $i['due_on']) ?></td>
        <td class="n">
          <form method="post" style="display:inline-flex;gap:6px"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $i['id'] ?>">
            <?php if ($i['status'] === 'issued'): ?>
              <button class="btn sm dark" name="do" value="paid">Πληρώθηκε</button>
              <button class="btn sm danger" name="do" value="void" onclick="return confirm('Ακύρωση του <?= e($i['number']) ?>;')">Ακύρωση</button>
            <?php elseif ($i['status'] === 'paid'): ?>
              <button class="btn sm" name="do" value="unpaid">Αναίρεση</button>
            <?php endif; ?>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
    <?php if (!$invoices): ?><tr><td colspan="6" class="empty">Κανένα παραστατικό.</td></tr><?php endif; ?>
    </tbody>
  </table>
</div>
