<?php
$preview = $preview ?? false;
$called = $_GET['called'] ?? '';
$hasWifi = !empty($profile['wifi_ssid']);
?>
<?php if ($preview): ?><div class="previewbar">Προεπισκόπηση · έτσι βλέπουν οι πελάτες το μενού σου</div><?php endif; ?>
<header class="hero<?= $profile['cover'] ? ' has-cover' : '' ?>"<?= $profile['cover'] ? ' style="background-image:url(\'' . e(upload_url($profile['cover'])) . '\')"' : '' ?>>
  <div class="heroin">
    <?php if ($profile['logo']): ?><img class="logo" src="<?= e(upload_url($profile['logo'])) ?>" alt=""><?php endif; ?>
    <h1><?= e($profile['title']) ?></h1>
    <?php if ($profile['subtitle']): ?><p class="sub"><?= e($profile['subtitle']) ?></p><?php endif; ?>
  </div>
</header>

<div class="info">
  <?php if ($profile['hours']): ?><span>🕒 <?= e($profile['hours']) ?></span><?php endif; ?>
  <?php if ($hasWifi): ?><details><summary>📶 Wi-Fi</summary><div><b><?= e($profile['wifi_ssid']) ?></b><?= $profile['wifi_pass'] ? ' · ' . e($profile['wifi_pass']) : '' ?></div></details><?php endif; ?>
  <?php if ($profile['phone']): ?><a href="tel:<?= e(preg_replace('/[^0-9+]/', '', $profile['phone'])) ?>">📞 <?= e($profile['phone']) ?></a><?php endif; ?>
</div>

<?php if ($called): ?>
  <div class="toast"><?= $called === 'bill' ? '✓ Ο λογαριασμός έρχεται!' : '✓ Ο σερβιτόρος ειδοποιήθηκε και έρχεται.' ?></div>
<?php endif; ?>

<?php if (count($categories) > 1): ?>
<nav class="cats" id="cats">
  <?php foreach ($categories as $i => $c): ?><a href="#c<?= (int) $c['id'] ?>" class="<?= $i === 0 ? 'on' : '' ?>"><?= e($c['name']) ?></a><?php endforeach; ?>
</nav>
<?php endif; ?>

<main class="menu">
  <?php foreach ($categories as $c): ?>
    <section id="c<?= (int) $c['id'] ?>">
      <h2><?= e($c['name']) ?></h2>
      <?php if ($c['note']): ?><p class="note"><?= e($c['note']) ?></p><?php endif; ?>
      <?php foreach ($items[(int) $c['id']] as $it): ?>
        <article class="item<?= $it['available'] ? '' : ' out' ?>">
          <div class="txt">
            <h3><?= e($it['name']) ?></h3>
            <?php if ($it['description']): ?><p><?= e($it['description']) ?></p><?php endif; ?>
            <div class="meta">
              <?php if ($it['price_cents'] !== null): ?><span class="price"><?= money($it['price_cents']) ?></span><?php endif; ?>
              <?php if (!$it['available']): ?><span class="tag out">Εξαντλήθηκε</span><?php endif; ?>
              <?php foreach (array_filter(explode(',', (string) $it['tags'])) as $t): ?><?php if (isset(QR_TAGS[$t])): ?><span class="tag t-<?= e($t) ?>"><?= e(QR_TAGS[$t]) ?></span><?php endif; ?><?php endforeach; ?>
            </div>
          </div>
          <?php if ($it['photo']): ?><img src="<?= e(upload_url($it['photo'])) ?>" alt="" loading="lazy"><?php endif; ?>
        </article>
      <?php endforeach; ?>
    </section>
  <?php endforeach; ?>
  <?php if (!$categories): ?><p class="empty">Το μενού ετοιμάζεται. Ρωτήστε το προσωπικό.</p><?php endif; ?>

  <footer>
    <?php if ($profile['google_review_url']): ?><a class="cta ghost" href="<?= e($profile['google_review_url']) ?>" target="_blank" rel="noopener">⭐ Αξιολογήστε μας στο Google</a><?php endif; ?>
    <div class="social">
      <?php if ($profile['instagram']): ?><a href="<?= e($profile['instagram']) ?>" target="_blank" rel="noopener">Instagram</a><?php endif; ?>
      <?php if ($profile['facebook']): ?><a href="<?= e($profile['facebook']) ?>" target="_blank" rel="noopener">Facebook</a><?php endif; ?>
    </div>
    <?php if ($profile['address']): ?><p><?= e($profile['address']) ?></p><?php endif; ?>
    <?php if ($profile['footer']): ?><p><?= e($profile['footer']) ?></p><?php endif; ?>
    <p class="by">Με το QR Boss της MyMedia</p>
  </footer>
</main>

<?php if ($canCall): ?>
  <div class="callbar">
    <span class="tbl"><?= e($qr['table_label']) ?></span>
    <?php if ($preview): ?>
      <button class="cta" type="button" disabled>🔔 Σερβιτόρος</button><button class="cta ghost" type="button" disabled>🧾 Λογαριασμός</button>
    <?php else: ?>
      <form method="post" action="<?= e(url('q/' . $qr['code'] . '/call')) ?>"><?= csrf_field() ?><input type="hidden" name="kind" value="waiter"><button class="cta">🔔 Σερβιτόρος</button></form>
      <form method="post" action="<?= e(url('q/' . $qr['code'] . '/call')) ?>"><?= csrf_field() ?><input type="hidden" name="kind" value="bill"><button class="cta ghost">🧾 Λογαριασμός</button></form>
    <?php endif; ?>
  </div>
<?php endif; ?>

<script>
// Επισήμανση κατηγορίας καθώς κάνεις scroll
(function () {
  var nav = document.getElementById('cats'); if (!nav || !('IntersectionObserver' in window)) return;
  var links = nav.querySelectorAll('a');
  var io = new IntersectionObserver(function (es) {
    es.forEach(function (en) {
      if (!en.isIntersecting) return;
      links.forEach(function (a) {
        var on = a.getAttribute('href') === '#' + en.target.id;
        a.classList.toggle('on', on);
        if (on) nav.scrollTo({ left: a.offsetLeft - 16, behavior: 'smooth' });
      });
    });
  }, { rootMargin: '-45% 0px -50% 0px' });
  document.querySelectorAll('.menu section').forEach(function (s) { io.observe(s); });
})();
</script>
