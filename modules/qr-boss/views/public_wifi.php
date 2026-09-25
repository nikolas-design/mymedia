<main class="center">
  <?php if ($profile['logo']): ?><img class="logo" src="<?= e(upload_url($profile['logo'])) ?>" alt=""><?php endif; ?>
  <h1><?= e($profile['title']) ?></h1>
  <?php if ($profile['wifi_ssid']): ?>
    <div class="wifi">
      <small>Δίκτυο</small><b><?= e($profile['wifi_ssid']) ?></b>
      <?php if ($profile['wifi_pass']): ?>
        <small>Κωδικός</small><b id="pw"><?= e($profile['wifi_pass']) ?></b>
        <button class="cta" onclick="navigator.clipboard&&navigator.clipboard.writeText(document.getElementById('pw').textContent).then(function(){event.target.textContent='Αντιγράφηκε ✓'})">Αντιγραφή κωδικού</button>
      <?php endif; ?>
    </div>
  <?php else: ?>
    <p>Ρωτήστε το προσωπικό για τον κωδικό Wi-Fi.</p>
  <?php endif; ?>
</main>
