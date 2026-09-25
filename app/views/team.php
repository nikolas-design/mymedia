<?php $activeCount = count(array_filter($members, fn($m) => $m['active'])); ?>
<span class="eyebrow"><?= icon('users', 12) ?> Ομάδα</span>
<h1>Η ομάδα σου. <em>Οργανωμένη.</em></h1>
<p class="lead"><?= $activeCount ?> <?= $activeCount === 1 ? 'μέλος' : 'μέλη' ?><?php if ($invites): ?> · <?= count($invites) ?> <?= count($invites) === 1 ? 'εκκρεμής πρόσκληση' : 'εκκρεμείς προσκλήσεις' ?><?php endif; ?></p>

<?php if ($inviteLink): ?>
  <div class="card">
    <div class="cardhd"><?= tile('mail', 'green') ?><div><b>Η πρόσκληση είναι έτοιμη</b><small>Στάλθηκε με email. Μπορείς να στείλεις και τον σύνδεσμο με Viber ή WhatsApp.</small></div></div>
    <div class="copy"><input id="invlink" value="<?= e($inviteLink) ?>" readonly><button class="btn sm" type="button" data-copy="#invlink">Αντιγραφή</button></div>
  </div>
<?php endif; ?>

<div class="cols">
<div>
  <p class="sechd" style="margin-top:0">Μέλη</p>
  <div class="card lst">
    <?php foreach ($members as $m): $self = (int) $m['user_id'] === (int) $me['id']; ?>
      <?php if ($isOwner && $editId === (int) $m['id'] && !$self): ?>
        <form class="lr" method="post" style="display:block">
          <?= csrf_field() ?><input type="hidden" name="action" value="update"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
          <b><?= e($m['name']) ?></b><small><?= e($m['email']) ?></small>
          <div class="row2">
            <div><label>Ρόλος</label><select name="role"><?php foreach (ROLE_LABELS as $k => $v): ?><option value="<?= $k ?>" <?= $m['role'] === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select></div>
            <div><label>Θέση</label><input name="job_title" value="<?= e($m['job_title']) ?>" placeholder="π.χ. Barista"></div>
          </div>
          <label><input type="checkbox" name="active" value="1" <?= $m['active'] ? 'checked' : '' ?>> Ενεργό μέλος</label>
          <div class="btns"><button class="btn dark sm" type="submit">Αποθήκευση</button><a class="btn sm" href="<?= e(url('team')) ?>">Άκυρο</a></div>
        </form>
      <?php else: ?>
        <div class="lr">
          <span class="av" style="<?= avatar_style($m['name']) ?>"><?= e(initials($m['name'])) ?></span>
          <span class="grow"><b><?= e($m['name']) ?></b>
            <small><?= e(ROLE_LABELS[$m['role']]) ?><?= $self ? ' · εσύ' : ($m['job_title'] ? ' · ' . e($m['job_title']) : '') ?><?= !$self && $m['last_login_at'] ? ' · σύνδεση ' . e(ago($m['last_login_at'])) : '' ?></small></span>
          <?php if (!$m['active']): ?><?= pill('Ανενεργό', 'grey') ?><?php endif; ?>
          <?php if ($isOwner && !$self): ?><a class="btn sm" href="<?= e(url('team?edit=' . $m['id'])) ?>">Αλλαγή</a><?php endif; ?>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>
  </div>

  <?php if ($invites): ?>
    <p class="sechd">Εκκρεμείς προσκλήσεις</p>
    <div class="card lst">
      <?php foreach ($invites as $i): $expired = strtotime($i['expires_at']) < time(); ?>
        <div class="lr">
          <?= tile('mail', 'purple') ?>
          <span class="grow"><b><?= e($i['email']) ?></b>
            <small><?= e(ROLE_LABELS[$i['role']]) ?> · <?= $expired ? 'έληξε' : 'λήγει ' . e(date_gr($i['expires_at'], false)) ?></small></span>
          <?php if ($canInvite): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="resend"><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><button class="btn sm" type="submit">Ξανά</button></form>
            <form method="post" data-confirm="Ακύρωση της πρόσκλησης;"><?= csrf_field() ?><input type="hidden" name="action" value="cancel"><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><button class="btn sm danger" type="submit" aria-label="Ακύρωση"><?= icon('x', 14) ?></button></form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<div>
  <?php if ($canInvite): ?>
    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="invite">
      <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'purple') ?><div><b>Πρόσκληση μέλους</b><small>Θα λάβει email με σύνδεσμο εισόδου.</small></div></div>
      <label for="email">Email</label>
      <input id="email" name="email" type="email" required>
      <div class="row2">
        <div><label for="role">Ρόλος</label>
          <select id="role" name="role">
            <option value="member">Μέλος</option>
            <?php if ($isOwner): ?><option value="manager">Υπεύθυνος</option><option value="owner">Ιδιοκτήτης</option><?php endif; ?>
          </select></div>
        <div><label for="job_title">Θέση (προαιρετικό)</label><input id="job_title" name="job_title" placeholder="π.χ. Σερβιτόρα"></div>
      </div>
      <button class="btn dark" type="submit"><?= icon('plus', 16) ?> Αποστολή πρόσκλησης</button>
    </form>
  <?php endif; ?>
  <div class="card">
    <div class="cardhd"><?= tile('shield', 'green') ?><div><b>Ρόλοι</b><small>Τι βλέπει ο καθένας</small></div></div>
    <p class="small mb0" style="line-height:1.7"><b>Ιδιοκτήτης:</b> όλα, μαζί με τις χρεώσεις και τους ρόλους.<br>
      <b>Υπεύθυνος:</b> όλα τα εργαλεία και την ομάδα, όχι τις χρεώσεις.<br>
      <b>Μέλος:</b> τα εργαλεία της επιχείρησης.</p>
  </div>
</div>
</div>
