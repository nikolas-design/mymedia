<?= of_tabs('suppliers') ?>
<a class="back" href="<?= e(module_url('orderflow', 'suppliers')) ?>"><?= icon('back', 14) ?> Προμηθευτές</a>
<div class="cols">
<form class="card" method="post">
  <?= csrf_field() ?><input type="hidden" name="action" value="supplier">
  <div class="cardhd" style="margin-bottom:0"><?= tile('truck', 'indigo') ?><div><b><?= $isNew ? 'Νέος προμηθευτής' : e($sup['name']) ?></b></div></div>
  <label>Επωνυμία</label><input name="name" value="<?= e($sup['name'] ?? '') ?>" required <?= $canEdit ? '' : 'disabled' ?>>
  <div class="row2">
    <div><label>Υπεύθυνος</label><input name="contact_name" value="<?= e($sup['contact_name'] ?? '') ?>"></div>
    <div><label>Κινητό (Viber/WhatsApp)</label><input name="phone" value="<?= e($sup['phone'] ?? '') ?>" type="tel"></div>
  </div>
  <label>Email παραγγελιών</label><input name="email" value="<?= e($sup['email'] ?? '') ?>" type="email">
  <label>Ημέρες παραγγελίας</label><input name="order_days" value="<?= e($sup['order_days'] ?? '') ?>" placeholder="π.χ. Δευτέρα & Πέμπτη έως 12:00">
  <label>Σημειώσεις</label><input name="notes" value="<?= e($sup['notes'] ?? '') ?>">
  <?php if (!$isNew): ?><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $sup['active'] ? 'checked' : '' ?>> Ενεργός</label><?php endif; ?>
  <?php if ($canEdit): ?><button class="btn dark"><?= $isNew ? 'Προσθήκη' : 'Αποθήκευση' ?></button><?php endif; ?>
</form>
<?php if (!$isNew): ?>
<div>
  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="products">
    <div class="cardhd" style="margin-bottom:0"><?= tile('grid', 'purple') ?><div><b>Είδη (<?= count($products) ?>)</b><small>Όνομα, μονάδα, τιμή (προαιρετική, για εκτίμηση κόστους).</small></div></div>
    <?php foreach ([...$products, ...array_fill(0, 3, null)] as $i => $p): $key = $p ? (int) $p['id'] : 'n' . $i; ?>
      <div class="inline mt" style="gap:6px">
        <input name="p[<?= $key ?>][name]" value="<?= e($p['name'] ?? '') ?>" placeholder="<?= $p ? '' : 'Νέο είδος' ?>">
        <input name="p[<?= $key ?>][unit]" value="<?= e($p['unit'] ?? '') ?>" placeholder="κιβώτιο" style="width:100px">
        <input name="p[<?= $key ?>][price]" value="<?= isset($p['price_cents']) ? e(number_format($p['price_cents'] / 100, 2, ',', '')) : '' ?>" placeholder="€" inputmode="decimal" style="width:80px">
        <?php if ($p): ?><label title="Διαγραφή" style="margin:0"><input type="checkbox" name="p[<?= $key ?>][delete]" value="1"> ✕</label><?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if ($canEdit): ?><button class="btn dark">Αποθήκευση ειδών</button><?php endif; ?>
  </form>
  <a class="btn" href="<?= e(module_url('orderflow', 'new/' . $sup['id'])) ?>">Νέα παραγγελία σε <?= e($sup['name']) ?></a>
  <?php if ($canEdit): ?><form method="post" data-confirm="Διαγραφή του προμηθευτή, των ειδών και των παραγγελιών του;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή προμηθευτή</button></form><?php endif; ?>
</div>
<?php endif; ?>
</div>
