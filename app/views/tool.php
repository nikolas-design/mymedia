<?php
$features = array_filter(array_map('trim', explode("\n", (string) $tool['features'])));
$cheapest = $plans ? min(array_map(fn($p) => $p['period'] === 'year' ? (int) round($p['price_cents'] / 12) : (int) $p['price_cents'], $plans)) : null;
$hasMonthly = (bool) array_filter($plans, fn($p) => $p['period'] === 'month');
?>
<a class="back" href="<?= e(url('tools')) ?>"><?= icon('back', 14) ?> Εργαλεία</a>
<div class="cols">
<div>
  <p class="num"><?= str_pad((string) $number, 2, '0', STR_PAD_LEFT) ?>
    <?php if ($subscription): ?><?= pill('● Ενεργό', 'green') ?>
    <?php elseif ($request): ?><?= pill('● Αίτημα σε εξέλιξη', 'purple') ?>
    <?php elseif ($tool['status'] === 'soon'): ?><?= pill('● Σύντομα', 'grey') ?>
    <?php else: ?><?= pill('● Διαθέσιμο τώρα', 'green') ?><?php endif; ?>
  </p>
  <h1 style="margin-top:6px"><?= e($tool['name']) ?></h1>
  <p class="lead" style="font-size:17px;color:var(--ink)"><?= e($tool['tagline']) ?></p>
  <?php if ($tool['description']): ?><p class="body"><?= e($tool['description']) ?></p><?php endif; ?>
  <?php if ($tool['audience']): ?>
    <div class="dfor"><small>ΣΧΕΔΙΑΣΜΕΝΟ ΓΙΑ</small><b><?= e($tool['audience']) ?></b></div>
  <?php endif; ?>
  <?php if ($features): ?>
    <ul class="checks"><?php foreach ($features as $f): ?><li><?= e($f) ?></li><?php endforeach; ?></ul>
  <?php endif; ?>
  <?php if ($cheapest !== null && !$subscription): ?>
    <div class="pricerow"><div><small>Από</small><b><?= money($cheapest) ?></b> <span>/ μήνα + ΦΠΑ</span></div></div>
  <?php endif; ?>
</div>

<div>
<?php if ($subscription): ?>
  <div class="card">
    <div class="cardhd"><?= tile($tool['icon'], $tool['color']) ?><div><b><?= e($tool['name']) ?> · <?= e($subscription['plan_name']) ?></b>
      <small><?= money($subscription['price_cents']) ?><?= period_label($subscription['billing']) ?> · ανανέωση <?= e(date_gr($subscription['renews_on'])) ?></small></div></div>
    <a class="btn dark" href="<?= e(module_url($tool['slug'])) ?>">Άνοιγμα <?= e($tool['name']) ?> <?= icon('arrow', 16) ?></a>
  </div>
<?php elseif ($request): ?>
  <div class="card">
    <div class="cardhd"><?= tile('bolt', 'purple') ?><div><b>Το αίτημά σου</b><small>Υποβλήθηκε <?= e(ago($request['created_at'])) ?></small></div></div>
    <?php foreach (request_steps($request['status']) as [$label, $state]): ?>
      <div class="step"><span><?= e($label) ?></span><?= step_pill($state) ?></div>
    <?php endforeach; ?>
  </div>
<?php elseif (!$canRequest): ?>
  <div class="card"><p class="body mb0">Για ενεργοποίηση εργαλείων μίλα με τον ιδιοκτήτη ή τον υπεύθυνο της επιχείρησης.</p></div>
<?php elseif ($tool['status'] === 'soon'): ?>
  <form class="card" method="post">
    <?= csrf_field() ?>
    <div class="cardhd"><?= tile('bell', 'purple') ?><div><b>Έρχεται σύντομα</b><small>Θα σε ενημερώσουμε πρώτους.</small></div></div>
    <label for="note">Τι θα ήθελες να κάνει; (προαιρετικό)</label>
    <textarea id="note" name="note" rows="3"></textarea>
    <button class="btn dark" type="submit">Ενημέρωσέ με</button>
  </form>
<?php else: ?>
  <form method="post">
    <?= csrf_field() ?>
    <p class="sechd" style="margin-top:0">Διάλεξε πλάνο</p>
    <?php foreach ($plans as $i => $p): ?>
      <label class="card plan">
        <input type="radio" name="plan_id" value="<?= (int) $p['id'] ?>" <?= ($p['popular'] || ($i === 0 && !array_filter($plans, fn($x) => $x['popular']))) ? 'checked' : '' ?>>
        <span class="top"><b><?= e($p['name']) ?><?php if ($p['popular']): ?> <?= pill('Δημοφιλές', 'purple') ?><?php endif; ?></b>
          <span><?= money($p['price_cents']) ?><?= period_label($p['period']) ?></span></span>
        <small class="muted"><?= e($p['summary']) ?></small>
      </label>
    <?php endforeach; ?>
    <?php if ($hasMonthly): ?>
      <label for="billing">Χρέωση</label>
      <select id="billing" name="billing">
        <option value="month">Μηνιαία</option>
        <option value="year">Ετήσια: 2 μήνες δώρο</option>
      </select>
    <?php endif; ?>
    <label for="note">Σημείωση (προαιρετικό)</label>
    <textarea id="note" name="note" rows="3" placeholder="π.χ. θέλουμε και κλήση σερβιτόρου"></textarea>
    <button class="btn dark" type="submit">Ζητήστε ενεργοποίηση</button>
    <p class="c small muted">Χωρίς χρέωση τώρα. Επιβεβαιώνουμε μαζί σου και ενεργοποιούμε. Οι τιμές δεν περιλαμβάνουν ΦΠΑ <?= VAT_RATE ?>%.</p>
  </form>
<?php endif; ?>
</div>
</div>
