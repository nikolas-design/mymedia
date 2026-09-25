<span class="eyebrow"><?= icon('receipt', 12) ?> Χρεώσεις</span>
<h1>Μία μηνιαία χρέωση. <em>Χωρίς εκπλήξεις.</em></h1>

<div class="g2 g4">
  <div class="stat"><small>Μηνιαίο κόστος</small><b><?= money($monthly) ?></b></div>
  <div class="stat"><small>Ενεργές συνδρομές</small><b><?= count($subs) ?></b></div>
  <div class="stat<?= $balance ? ' dark' : '' ?>"><small>Ανοιχτό υπόλοιπο</small><b><?= money($balance) ?></b></div>
  <div class="stat"><small>Παραστατικά</small><b><?= count($invoices) ?></b></div>
</div>
<p class="small muted">Τα ποσά συνδρομών είναι χωρίς ΦΠΑ. Τα παραστατικά περιλαμβάνουν ΦΠΑ <?= VAT_RATE ?>%.</p>

<div class="cols">
<div>
  <p class="sechd">Συνδρομές</p>
  <div class="card lst">
    <?php foreach ($subs as $s): ?>
      <a class="lr" href="<?= e(url('tools/' . $s['slug'])) ?>">
        <?= tile($s['icon'], $s['color']) ?>
        <span class="grow"><b><?= e($s['tool_name']) ?> · <?= e($s['plan_name']) ?></b><small><?= money($s['price_cents']) ?><?= period_label($s['billing']) ?> · ανανέωση <?= e(date_gr($s['renews_on'], false)) ?></small></span>
        <?= pill('Ενεργό', 'green') ?>
      </a>
    <?php endforeach; ?>
    <?php if (!$subs): ?><div class="empty">Καμία ενεργή συνδρομή.</div><?php endif; ?>
  </div>

  <div class="card">
    <div class="cardhd"><?= tile('building', 'blue') ?><div><b>Τρόπος πληρωμής</b><small>Κατάθεση σε τράπεζα</small></div></div>
    <p class="small mono mb0"><?= e(setting('bank_name')) ?><br><?= e(setting('bank_iban')) ?><br>Δικαιούχος: <?= e(setting('bank_beneficiary')) ?><br>Αιτιολογία: αριθμός παραστατικού</p>
  </div>
</div>
<div>
  <p class="sechd">Παραστατικά</p>
  <div class="card lst">
    <?php foreach ($invoices as $i): ?>
      <a class="lr" href="<?= e(url('invoices/' . $i['id'])) ?>">
        <?= tile('receipt', 'grey') ?>
        <span class="grow"><b><?= e($i['number']) ?></b><small><?= e(date_gr($i['issued_on'])) ?><?= $i['status'] === 'issued' && $i['due_on'] ? ' · έως ' . e(date_gr($i['due_on'], false)) : '' ?></small></span>
        <span class="r"><b><?= money($i['total_cents']) ?></b><?= invoice_pill($i['status'], $i['due_on']) ?></span>
      </a>
    <?php endforeach; ?>
    <?php if (!$invoices): ?><div class="empty">Δεν υπάρχουν ακόμα παραστατικά.</div><?php endif; ?>
  </div>
</div>
</div>
