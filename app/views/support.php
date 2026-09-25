<span class="eyebrow"><?= icon('headset', 12) ?> Υποστήριξη</span>
<h1>Είμαστε εδώ. <em>Την ίδια ημέρα.</em></h1>
<p class="lead">Γράψε μας για οτιδήποτε: πρόβλημα, αλλαγή ή ιδέα.</p>
<div class="cols">
  <form class="card" method="post" id="new">
    <?= csrf_field() ?>
    <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'pink') ?><div><b>Νέο αίτημα</b><small>Θα λάβεις απάντηση εδώ.</small></div></div>
    <label for="subject">Θέμα</label>
    <input id="subject" name="subject" maxlength="160" required placeholder="π.χ. Δεν εκτυπώνεται το QR μενού">
    <label for="body">Μήνυμα</label>
    <textarea id="body" name="body" required></textarea>
    <button class="btn dark" type="submit">Αποστολή</button>
  </form>
  <div>
    <p class="sechd" style="margin-top:0">Τα αιτήματά σου</p>
    <div class="card lst">
      <?php foreach ($tickets as $t): ?>
        <a class="lr" href="<?= e(url('support/' . $t['id'])) ?>">
          <?= tile('headset', $t['status'] === 'closed' ? 'grey' : 'pink') ?>
          <span class="grow"><b><?= e($t['subject']) ?></b><small><?= (int) $t['n'] ?> μηνύματα · <?= e(ago($t['updated_at'])) ?></small></span>
          <?= ticket_pill($t['status']) ?>
        </a>
      <?php endforeach; ?>
      <?php if (!$tickets): ?><div class="empty">Κανένα αίτημα ακόμα.</div><?php endif; ?>
    </div>
  </div>
</div>
