<?= ai_tabs('generate') ?>
<?php if (!$enabled): ?>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('logo', 'purple') ?><div><b>Το AI ενεργοποιείται σύντομα</b><small>Μέχρι τότε, η ομάδα της MyMedia γράφει τα posts για εσένα. Στείλε μας τι θέλεις.</small></div></div>
    <a class="btn dark" href="<?= e(url('support')) ?>#new">Ζήτα posts από την ομάδα</a>
  </div>
<?php else: ?>
<form method="post" class="cols" id="genform">
  <?= csrf_field() ?>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('logo', 'purple') ?><div><b>Τι να ετοιμάσει το AI;</b><small><?= $used ?> / <?= AI_MONTHLY_RUNS ?> παραγωγές αυτόν τον μήνα</small></div></div>
    <div class="row2">
      <div><label>Από</label><input type="date" name="from" value="<?= e(date('Y-m-d', strtotime('+1 day'))) ?>"></div>
      <div><label>Έως</label><input type="date" name="to" value="<?= e(date('Y-m-d', strtotime('+7 days'))) ?>"></div>
    </div>
    <label>Πόσα posts</label><select name="count"><?php foreach ([3, 4, 5, 6, 8, 10, 12] as $n): ?><option <?= $n === 4 ? 'selected' : '' ?>><?= $n ?></option><?php endforeach; ?></select>
    <label>Πλατφόρμες</label>
    <div class="btns"><?php foreach (AI_PLATFORMS as $k => $v): ?><label class="btn sm" style="font-weight:500;gap:6px"><input type="checkbox" name="platforms[]" value="<?= $k ?>" <?= in_array($k, explode(',', $profile['platforms']), true) ? 'checked' : '' ?>> <?= e($v) ?></label><?php endforeach; ?></div>
  </div>
  <div class="card">
    <label style="margin-top:0">Θέμα ή κάτι που θέλεις να προωθήσεις (προαιρετικό)</label>
    <textarea name="theme" rows="5" placeholder="π.χ. Ξεκινάμε brunch Σαββατοκύριακου 10:00–14:00. Νέο πιάτο: pancakes με φιστίκι. Την Παρασκευή έχουμε live μουσική."></textarea>
    <button class="btn dark" id="genbtn">✨ Φτιάξε προτάσεις</button>
    <p class="hint">Παίρνει συνήθως 20–60 δευτερόλεπτα. Όλα τα posts μένουν ως προτάσεις μέχρι να τα εγκρίνεις.</p>
  </div>
</form>
<script>document.getElementById('genform').addEventListener('submit', function () { var b = document.getElementById('genbtn'); b.disabled = true; b.textContent = '✨ Γράφω… (έως 1 λεπτό)'; });</script>
<?php endif; ?>
