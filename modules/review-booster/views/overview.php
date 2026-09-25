<?= rb_tabs('overview') ?>
<?php foreach ($noGoogle as $l): ?>
  <div class="flash info">Το σημείο «<?= e($l['name']) ?>» δεν έχει σύνδεσμο κριτικής Google, οπότε οι ευχαριστημένοι πελάτες δεν θα οδηγούνται εκεί.
    <?php if ($canEdit): ?><a href="<?= e(module_url('review-booster', 'locations/' . $l['id'])) ?>">Πρόσθεσέ τον</a><?php endif; ?></div>
<?php endforeach; ?>

<div class="g2 g4">
  <div class="stat dark"><small>Μέση βαθμολογία (30 ημ.)</small><b><?= $s30['avg'] !== null ? number_format($s30['avg'], 1, ',', '') : '—' ?></b> <span class="small" style="color:#f5b301">★</span></div>
  <div class="stat"><small>Αξιολογήσεις</small><b><?= $s30['count'] ?></b></div>
  <div class="stat"><small>Πήγαν στο Google</small><b><?= $s30['google'] ?></b></div>
  <div class="stat"><small>Ιδιωτικά σχόλια</small><b><?= $s30['feedback'] ?></b></div>
</div>

<div class="cols mt">
<div>
  <div class="card">
    <div class="cardhd"><?= tile('star', 'orange') ?><div><b>Κατανομή αστεριών</b><small>Τελευταίες 30 ημέρες<?= $s30['positive'] !== null ? ' · ' . $s30['positive'] . '% θετικές' : '' ?></small></div></div>
    <?php $max = max(1, max($s30['dist'])); foreach ($s30['dist'] as $st => $n): ?>
      <div class="hb"><span><?= rb_stars($st) ?></span><i><b style="width:<?= round($n / $max * 100) ?>%;background:<?= $st >= 4 ? '#22a06b' : ($st === 3 ? '#e79b23' : '#d64b7c') ?>"></b></i><em><?= $n ?></em></div>
    <?php endforeach; ?>
  </div>
  <div class="card">
    <div class="cardhd"><?= tile('chart', 'blue') ?><div><b>Αξιολογήσεις ανά ημέρα</b><small>Τελευταίες 14 ημέρες</small></div></div>
    <?= day_bars($daily) ?>
  </div>
</div>
<div>
  <div class="card">
    <div class="cardhd"><?= tile('shield', 'green') ?><div><b>Πώς δουλεύει</b></div></div>
    <p class="small" style="line-height:1.6;margin:0">1. Ο πελάτης σκανάρει το QR ή ανοίγει τον σύνδεσμο και βάζει αστέρια.<br>
      2. <b>4–5 αστέρια:</b> τον οδηγούμε να γράψει κριτική στο Google.<br>
      3. <b>1–3 αστέρια:</b> σας γράφει ιδιωτικά εδώ, πριν φτάσει σε δημόσια κριτική.</p>
  </div>
  <p class="sechd">Νέα ιδιωτικά σχόλια</p>
  <div class="card lst">
    <?php foreach ($recent as $f): ?>
      <a class="lr" href="<?= e(module_url('review-booster', 'feedback/' . $f['id'])) ?>">
        <span class="grow"><b><?= rb_stars((int) $f['stars']) ?> <?= e($f['name'] ?: 'Ανώνυμα') ?></b><small><?= e($f['message']) ?></small></span>
        <span class="small muted"><?= e(ago($f['created_at'])) ?></span>
      </a>
    <?php endforeach; ?>
    <?php if (!$recent): ?><div class="empty">Κανένα νέο σχόλιο. 👍</div><?php endif; ?>
  </div>
</div>
</div>
