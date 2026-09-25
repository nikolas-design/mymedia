<h1>Εργαλεία & τιμές</h1>
<p class="lead">Κατάσταση, περιγραφές και πλάνα όπως τα βλέπουν οι πελάτες. Η ένδειξη «Module» δείχνει αν το εργαλείο έχει ήδη υλοποίηση.</p>
<div class="card lst" style="max-width:820px">
  <?php foreach ($tools as $t): ?>
    <a class="lr" href="<?= e(url('admin/tools/' . $t['id'])) ?>">
      <?= tile($t['icon'], $t['color']) ?>
      <span class="grow"><b><?= e($t['name']) ?></b><small><?= (int) $t['subs'] ?> συνδρομές · <?= (int) $t['plans'] ?> πλάνα · /t/<?= e($t['slug']) ?></small></span>
      <?= module_exists($t['slug']) ? pill('Module', 'green') : pill('Χωρίς module', 'grey') ?>
      <?php if (module_has_admin($t['slug'])): ?><object><a class="btn sm" href="<?= e(url('admin/t/' . $t['slug'])) ?>">Διαχείριση</a></object><?php endif; ?>
      <?= match ($t['status']) { 'available' => pill('Διαθέσιμο', 'purple'), 'soon' => pill('Σύντομα', 'orange'), default => pill('Κρυφό', 'grey') } ?>
    </a>
  <?php endforeach; ?>
</div>
