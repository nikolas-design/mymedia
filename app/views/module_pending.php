<a class="back" href="<?= e(url('tools/' . $tool['slug'])) ?>"><?= icon('back', 14) ?> <?= e($tool['name']) ?></a>
<div class="card c" style="padding:32px 20px">
  <?= tile($tool['icon'], $tool['color'], 56) ?>
  <h1><?= e($tool['name']) ?> <em>ετοιμάζεται.</em></h1>
  <p class="lead">Η συνδρομή σου είναι ενεργή και στήνουμε το εργαλείο για την επιχείρησή σου. Θα σε ειδοποιήσουμε μόλις είναι έτοιμο.</p>
  <a class="btn auto" href="<?= e(url('support')) ?>">Ερώτηση στην υποστήριξη</a>
</div>
