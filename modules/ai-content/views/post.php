<?= ai_tabs('posts') ?>
<a class="back" href="<?= e(module_url('ai-content')) ?>"><?= icon('back', 14) ?> Πλάνο posts</a>
<form method="post" class="cols">
  <?= csrf_field() ?>
  <div class="card">
    <div class="row2">
      <div><label style="margin-top:0">Ημερομηνία</label><input type="date" name="for_date" value="<?= e($p['for_date'] ?? date('Y-m-d')) ?>"></div>
      <div><label style="margin-top:0">Πλατφόρμα</label><select name="platform"><?php foreach (AI_PLATFORMS as $k => $v): ?><option value="<?= $k ?>" <?= ($p['platform'] ?? '') === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
    </div>
    <label>Τίτλος (για εσένα)</label><input name="title" value="<?= e($p['title'] ?? '') ?>">
    <label>Κείμενο</label><textarea name="caption" id="cap" rows="9" required><?= e($p['caption'] ?? '') ?></textarea>
    <label>Hashtags</label><input name="hashtags" id="tags" value="<?= e($p['hashtags'] ?? '') ?>">
    <label>Ιδέα για φωτογραφία / βίντεο</label><input name="image_idea" value="<?= e($p['image_idea'] ?? '') ?>">
  </div>
  <div>
    <div class="card">
      <label style="margin-top:0">Κατάσταση</label>
      <select name="status"><?php foreach (AI_STATUS as $k => [$l]): ?><option value="<?= $k ?>" <?= ($p['status'] ?? 'approved') === $k ? 'selected' : '' ?>><?= e($l) ?></option><?php endforeach; ?></select>
      <?php if ($canEdit): ?><button class="btn dark">Αποθήκευση</button><?php endif; ?>
      <button class="btn" type="button" id="copyall">📋 Αντιγραφή κειμένου + hashtags</button>
      <p class="hint">Αντέγραψε και επικόλλησε στο Instagram ή στο Facebook. Μετά άλλαξε την κατάσταση σε «Δημοσιεύτηκε».</p>
    </div>
    <?php if (!$isNew && $canEdit): ?><button class="btn danger" name="action" value="delete" formnovalidate onclick="return confirm('Διαγραφή;')">Διαγραφή</button><?php endif; ?>
  </div>
</form>
<script>
document.getElementById('copyall').addEventListener('click', function () {
  var t = document.getElementById('cap').value + (document.getElementById('tags').value ? '\n\n' + document.getElementById('tags').value : ''), b = this;
  navigator.clipboard.writeText(t).then(function () { b.textContent = 'Αντιγράφηκε ✓'; });
});
</script>
