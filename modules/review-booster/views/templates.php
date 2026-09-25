<?= rb_tabs('templates') ?>
<p class="lead">Έτοιμες απαντήσεις για τις κριτικές σου στο Google. Γράψε το όνομα του πελάτη, πάτα «Αντιγραφή» και επικόλλησε στο Google.</p>
<div class="card" style="max-width:520px"><label style="margin-top:0">Όνομα πελάτη</label><input id="cname" placeholder="π.χ. Μαρία"></div>
<div class="cols">
  <?php foreach ([...$templates, null] as $t): ?>
    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) ($t['id'] ?? 0) ?>">
      <div class="row2">
        <div><input name="title" value="<?= e($t['title'] ?? '') ?>" placeholder="Τίτλος" <?= $t ? '' : 'required' ?>></div>
        <div><select name="kind"><?php foreach (['positive' => 'Θετική', 'neutral' => 'Ουδέτερη', 'negative' => 'Αρνητική'] as $k => $v): ?><option value="<?= $k ?>" <?= ($t['kind'] ?? '') === $k ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select></div>
      </div>
      <textarea name="body" rows="4" class="mt tpl" data-biz="<?= e($business['name']) ?>" data-phone="<?= e($phone) ?>" placeholder="Νέο πρότυπο… Μπορείς να γράψεις {όνομα}, {επιχείρηση}, {τηλέφωνο}"><?= e($t['body'] ?? '') ?></textarea>
      <div class="btns">
        <?php if ($t): ?><button class="btn sm dark" type="button" data-fill>Αντιγραφή</button><?php endif; ?>
        <?php if ($canEdit): ?><button class="btn sm"><?= $t ? 'Αποθήκευση' : 'Προσθήκη' ?></button><?php endif; ?>
        <?php if ($t && $canEdit): ?><button class="btn sm danger" name="action" value="delete" formnovalidate onclick="return confirm('Διαγραφή;')">Διαγραφή</button><?php endif; ?>
      </div>
    </form>
  <?php endforeach; ?>
</div>
<script>
document.addEventListener('click', function (e) {
  var b = e.target.closest('[data-fill]'); if (!b) return;
  var ta = b.closest('form').querySelector('.tpl'), name = document.getElementById('cname').value.trim();
  var txt = ta.value.replace(/\{όνομα\}/g, name || '').replace(/\{επιχείρηση\}/g, ta.dataset.biz).replace(/\{τηλέφωνο\}/g, ta.dataset.phone || '')
    .replace(/\s+,/g, ',').replace(/  +/g, ' ');
  navigator.clipboard.writeText(txt).then(function () { b.textContent = 'Αντιγράφηκε ✓'; setTimeout(function () { b.textContent = 'Αντιγραφή'; }, 1500); });
});
</script>
