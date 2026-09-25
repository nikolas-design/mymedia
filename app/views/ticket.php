<a class="back" href="<?= e(url($back)) ?>"><?= icon('back', 14) ?> Υποστήριξη</a>
<h1 style="font-size:24px"><?= e($t['subject']) ?></h1>
<p class="lead"><?= ticket_pill($t['status']) ?><?= isset($t['business_name']) ? ' · ' . e($t['business_name']) : '' ?> · ανοίχτηκε <?= e(date_gr($t['created_at'])) ?></p>
<div class="wrap narrow" style="padding:0;margin:0;max-width:720px">
  <?php foreach ($messages as $m): ?>
    <div class="msg<?= $m['from_staff'] ? ' staff' : '' ?>">
      <div class="meta"><b><?= e($m['from_staff'] ? 'Ομάδα ' . setting('company_name', 'MyMedia') : ($m['name'] ?? '—')) ?></b> · <?= e(ago($m['created_at'])) ?></div>
      <div class="pre small" style="font-size:14.5px;line-height:1.55"><?= e($m['body']) ?></div>
    </div>
  <?php endforeach; ?>
  <form class="card mt" method="post">
    <?= csrf_field() ?>
    <label for="body" style="margin-top:0"><?= $staff ? 'Απάντηση στον πελάτη' : 'Απάντηση' ?></label>
    <textarea id="body" name="body" required></textarea>
    <div class="btns">
      <button class="btn dark" type="submit" name="do" value="reply">Αποστολή</button>
      <?php if ($staff && $t['status'] !== 'closed'): ?><button class="btn" type="submit" name="do" value="close" formnovalidate>Κλείσιμο θέματος</button><?php endif; ?>
    </div>
  </form>
</div>
