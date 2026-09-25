<h1>Αιτήματα</h1>
<div class="btns" style="margin-bottom:14px">
  <a class="btn sm<?= $status !== 'all' ? ' dark' : '' ?>" href="<?= e(url('admin/requests')) ?>">Ανοιχτά</a>
  <a class="btn sm<?= $status === 'all' ? ' dark' : '' ?>" href="<?= e(url('admin/requests?status=all')) ?>">Όλα</a>
</div>
<div class="card lst">
  <?php foreach ($requests as $r): $open = in_array($r['status'], ['new', 'setup'], true); ?>
    <div class="lr" style="flex-wrap:wrap">
      <span class="grow" style="min-width:220px"><b><a href="<?= e(url('admin/businesses/' . $r['business_id'])) ?>" style="color:inherit"><?= e($r['business_name']) ?></a> → <?= e($r['tool_name']) ?></b>
        <small><?= $r['plan_name'] ? e($r['plan_name']) . ' · ' . money($r['price_cents']) . period_label($r['period']) : 'Δήλωση ενδιαφέροντος' ?><?= $r['billing'] === 'year' && $r['period'] === 'month' ? ' · ετήσια (2 μήνες δώρο)' : '' ?> · <?= e($r['user_name'] ?? '') ?> · <?= e(ago($r['created_at'])) ?></small>
        <?php if ($r['note']): ?><small style="white-space:normal">«<?= e($r['note']) ?>»</small><?php endif; ?>
      </span>
      <?= request_pill($r['status']) ?>
      <?php if ($open): ?>
        <div class="btns">
          <?php if ($r['status'] === 'new'): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm" name="do" value="setup">Έγκριση</button></form>
          <?php endif; ?>
          <?php if ($r['plan_id']): ?>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm dark" name="do" value="activate">Ενεργοποίηση</button></form>
          <?php endif; ?>
          <form method="post" data-confirm="Απόρριψη του αιτήματος;"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int) $r['id'] ?>"><button class="btn sm danger" name="do" value="reject">Απόρριψη</button></form>
        </div>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$requests): ?><div class="empty">Κανένα αίτημα.</div><?php endif; ?>
</div>
