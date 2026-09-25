<?= rb_tabs('feedback') ?>
<a class="back" href="<?= e(module_url('review-booster', 'feedback')) ?>"><?= icon('back', 14) ?> Ιδιωτικά σχόλια</a>
<div class="cols">
  <div class="card">
    <div style="font-size:22px"><?= rb_stars((int) $f['stars']) ?></div>
    <p class="small muted"><?= e($f['location']) ?> · <?= e(date_gr($f['created_at'])) ?> <?= e(date('H:i', strtotime($f['created_at']))) ?></p>
    <p class="body pre"><?= e($f['message']) ?></p>
    <p class="small mb0"><b><?= e($f['name'] ?: 'Ανώνυμα') ?></b><?php if ($f['contact']): ?> · <?php
      $isMail = filter_var($f['contact'], FILTER_VALIDATE_EMAIL);
      $tel = preg_replace('/[^0-9+]/', '', $f['contact']); ?>
      <a href="<?= e($isMail ? 'mailto:' . $f['contact'] : 'tel:' . $tel) ?>"><?= e($f['contact']) ?></a><?php endif; ?></p>
  </div>
  <form class="card" method="post">
    <?= csrf_field() ?>
    <div class="cardhd" style="margin-bottom:0"><?= tile('check', 'green') ?><div><b>Τι κάναμε</b><small>Εσωτερική σημείωση, δεν τη βλέπει ο πελάτης.</small></div></div>
    <textarea name="note" rows="4" class="mt" placeholder="π.χ. Τηλεφώνησα, του κεράσαμε το επόμενο"><?= e($f['note']) ?></textarea>
    <div class="btns">
      <button class="btn dark" name="status" value="resolved">Λύθηκε</button>
      <button class="btn" name="status" value="new">Αποθήκευση</button>
    </div>
  </form>
</div>
