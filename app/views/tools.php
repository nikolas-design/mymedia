<span class="eyebrow purple">Η σειρά προϊόντων</span>
<h1>Ένα εργαλείο για κάθε <em>καθημερινή λειτουργία.</em></h1>
<p class="lead">Ξεκίνα με αυτό που χρειάζεσαι σήμερα και πρόσθεσε περισσότερα καθώς μεγαλώνεις.</p>

<div class="g2 gtools">
  <?php foreach ($tools as $t):
      $state = $states[(int) $t['id']] ?? ($t['status'] === 'soon' ? 'soon' : 'available'); ?>
    <a class="pt<?= $state === 'soon' ? ' soon' : '' ?>" href="<?= e(url('tools/' . $t['slug'])) ?>">
      <?= tile($t['icon'], $t['color'], 44) ?>
      <b><?= e($t['name']) ?></b>
      <small><?= e($t['short']) ?></small>
      <div class="mt"><?= match ($state) {
          'active' => pill('Ενεργό', 'green'),
          'requested' => pill('Αίτημα σε εξέλιξη', 'purple'),
          'soon' => pill('Σύντομα', 'grey'),
          default => pill('Διαθέσιμο', 'orange'),
      } ?></div>
    </a>
  <?php endforeach; ?>
</div>
