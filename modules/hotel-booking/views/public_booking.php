<?php if (!empty($_GET['new'])): ?><p class="big">🎉</p><?php endif; ?>
<h1><?= $b['status'] === 'pending' ? 'Λάβαμε το αίτημά σας' : ($b['status'] === 'cancelled' ? 'Η κράτηση ακυρώθηκε' : 'Η κράτησή σας') ?></h1>
<p class="lead"><?= $b['status'] === 'pending' ? 'Θα σας επιβεβαιώσουμε με email σύντομα.' : e($st['title']) ?></p>
<div class="pcard"><div class="list">
  <div class="it"><span class="muted">Δωμάτιο</span><b><?= e($b['room']) ?></b></div>
  <div class="it"><span class="muted">Άφιξη</span><b><?= e(date_gr($b['checkin'])) ?> · από <?= e($st['checkin']) ?></b></div>
  <div class="it"><span class="muted">Αναχώρηση</span><b><?= e(date_gr($b['checkout'])) ?> · έως <?= e($st['checkout']) ?></b></div>
  <div class="it"><span class="muted">Άτομα</span><b><?= (int) $b['guests'] ?></b></div>
  <div class="it"><span class="muted">Σύνολο</span><b><?= money($b['total_cents']) ?></b></div>
  <div class="it"><span class="muted">Κατάσταση</span><b><?= e(HB_STATUS[$b['status']][0]) ?></b></div>
</div></div>
<?php if ($st['policy']): ?><p class="small muted" style="white-space:pre-wrap;text-align:left"><?= e($st['policy']) ?></p><?php endif; ?>
<?php if ($st['phone']): ?><p class="small">Επικοινωνία: <a href="tel:<?= e($st['phone']) ?>"><?= e($st['phone']) ?></a></p><?php endif; ?>
