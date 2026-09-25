<div class="noprint" style="display:flex;justify-content:space-between;align-items:center;margin-bottom:10px">
  <a class="back" href="<?= e(url($admin ? 'admin/businesses/' . $inv['business_id'] : 'billing')) ?>"><?= icon('back', 14) ?> Πίσω</a>
  <button class="btn sm" type="button" data-print><?= icon('print', 14) ?> Εκτύπωση / PDF</button>
</div>
<div class="inv">
  <div style="display:flex;justify-content:space-between;gap:16px;flex-wrap:wrap">
    <div>
      <div class="brand"><?= tile('logo', 'purple', 28) ?> <?= e(setting('company_name', 'MyMedia')) ?></div>
      <p class="small muted" style="margin:8px 0 0">Αναλυτικό παραστατικό συνδρομών</p>
    </div>
    <div style="text-align:right">
      <div class="h2"><?= e($inv['number']) ?></div>
      <div class="small muted">Έκδοση <?= e(date_gr($inv['issued_on'])) ?><?= $inv['due_on'] ? ' · Πληρωμή έως ' . e(date_gr($inv['due_on'])) : '' ?></div>
      <div class="mt"><?= invoice_pill($inv['status'], $inv['due_on']) ?></div>
    </div>
  </div>

  <p class="sechd">Πελάτης</p>
  <p class="small mb0" style="line-height:1.6"><b><?= e($inv['legal_name'] ?: $inv['business_name']) ?></b><br>
    <?php if ($inv['vat_number']): ?>ΑΦΜ <?= e($inv['vat_number']) ?><?= $inv['tax_office'] ? ' · ΔΟΥ ' . e($inv['tax_office']) : '' ?><br><?php endif; ?>
    <?= e($inv['address']) ?></p>

  <table>
    <thead><tr><th>Περιγραφή</th><th class="n">Ποσό</th></tr></thead>
    <tbody>
      <?php foreach ($lines as $l): ?><tr><td><?= e($l['description']) ?></td><td class="n"><?= money($l['amount_cents']) ?></td></tr><?php endforeach; ?>
      <tr><td class="muted">Καθαρή αξία</td><td class="n"><?= money($inv['net_cents']) ?></td></tr>
      <tr><td class="muted">ΦΠΑ <?= (int) $inv['vat_rate'] ?>%</td><td class="n"><?= money($inv['vat_cents']) ?></td></tr>
      <tr class="tot"><td>Σύνολο</td><td class="n"><?= money($inv['total_cents']) ?></td></tr>
    </tbody>
  </table>

  <?php if ($inv['status'] === 'issued'): ?>
    <p class="sechd">Πληρωμή</p>
    <p class="small mono mb0"><?= e(setting('bank_name')) ?> · <?= e(setting('bank_iban')) ?><br>Δικαιούχος: <?= e(setting('bank_beneficiary')) ?><br>Αιτιολογία: <?= e($inv['number']) ?></p>
  <?php elseif ($inv['status'] === 'paid'): ?>
    <p class="small muted mt">Εξοφλήθηκε <?= e(date_gr($inv['paid_on'])) ?>.</p>
  <?php endif; ?>
</div>
