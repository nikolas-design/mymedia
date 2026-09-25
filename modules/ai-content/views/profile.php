<?= ai_tabs('profile') ?>
<form method="post" class="cols"><?= csrf_field() ?>
  <div class="card">
    <label style="margin-top:0">Πες μας για την επιχείρησή σου</label>
    <textarea name="description" rows="5" placeholder="π.χ. Καφέ-μπαρ στο κέντρο της Αθήνας από το 2015. Brunch το πρωί, cocktails το βράδυ, live μουσική τις Παρασκευές."><?= e($profile['description']) ?></textarea>
    <label>Προϊόντα / υπηρεσίες που θέλεις να προβάλλονται</label>
    <textarea name="products" rows="4" placeholder="π.χ. Freddo espresso, pancakes, signature cocktails, ιδιωτικά πάρτι"><?= e($profile['products']) ?></textarea>
    <label>Κοινό</label><input name="audience" value="<?= e($profile['audience']) ?>" placeholder="π.χ. νέοι 20–35, φοιτητές, εργαζόμενοι της γειτονιάς">
  </div>
  <div class="card">
    <label style="margin-top:0">Ύφος</label><input name="tone" value="<?= e($profile['tone']) ?>" placeholder="π.χ. χαλαρό, με χιούμορ, πληθυντικός ευγενείας">
    <label>Να αποφεύγει</label><input name="avoid" value="<?= e($profile['avoid']) ?>" placeholder="π.χ. αγγλικές λέξεις, τιμές, υπερθετικά">
    <label>Σταθερά hashtags</label><input name="hashtags" value="<?= e($profile['hashtags']) ?>" placeholder="π.χ. #karagiozisclub #athens">
    <label>Πλατφόρμες</label>
    <div class="btns"><?php foreach (AI_PLATFORMS as $k => $v): ?><label class="btn sm" style="font-weight:500;gap:6px"><input type="checkbox" name="platforms[]" value="<?= $k ?>" <?= in_array($k, explode(',', $profile['platforms']), true) ? 'checked' : '' ?>> <?= e($v) ?></label><?php endforeach; ?></div>
    <label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="emojis" value="1" <?= $profile['emojis'] ? 'checked' : '' ?>> Με emojis</label>
    <button class="btn dark">Αποθήκευση</button>
  </div>
</form>
