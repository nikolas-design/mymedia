<h1><?= e($loc['question'] ?: 'Πώς ήταν η εμπειρία σας;') ?></h1>
<p class="lead"><?= e($bizName) ?><?= $loc['name'] !== $bizName ? ' · ' . e($loc['name']) : '' ?></p>
<form method="post" class="pcard" style="text-align:center">
  <?= csrf_field() ?><input type="hidden" name="source" value="<?= e($source) ?>">
  <div class="stars" role="group" aria-label="Βαθμολογία">
    <?php for ($i = 1; $i <= 5; $i++): ?><button name="stars" value="<?= $i ?>" aria-label="<?= $i ?> αστέρια">★</button><?php endfor; ?>
  </div>
  <div class="slabels"><span>Κακή</span><span>Εξαιρετική</span></div>
</form>
<p class="small muted">Πατήστε ένα αστέρι. Παίρνει 10 δευτερόλεπτα.</p>
