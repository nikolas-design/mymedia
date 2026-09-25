<?= ro_tabs('menu') ?>
<div class="cols">
<div>
  <?php foreach ($categories as $c): ?>
    <div style="display:flex;justify-content:space-between;align-items:center;margin:18px 0 8px"><p class="sechd" style="margin:0"><?= e($c['name']) ?></p>
      <form method="post" data-confirm="Διαγραφή κατηγορίας και πιάτων;"><?= csrf_field() ?><input type="hidden" name="action" value="delete_category"><input type="hidden" name="id" value="<?= (int) $c['id'] ?>"><button class="btn sm danger">✕</button></form></div>
    <div class="card lst">
      <?php foreach ($items[(int) $c['id']] ?? [] as $i): ?>
        <?php if ($edit === (int) $i['id']): ?>
          <form class="lr" method="post" style="display:block"><?= csrf_field() ?><input type="hidden" name="action" value="item"><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><input type="hidden" name="category_id" value="<?= (int) $c['id'] ?>">
            <div class="row2"><input name="name" value="<?= e($i['name']) ?>" required><input name="price" value="<?= e(number_format($i['price_cents'] / 100, 2, ',', '')) ?>" required inputmode="decimal"></div>
            <input class="mt" name="description" value="<?= e($i['description']) ?>" placeholder="Περιγραφή">
            <label style="display:flex;gap:6px;align-items:center"><input type="checkbox" name="available" value="1" <?= $i['available'] ? 'checked' : '' ?>> Διαθέσιμο</label>
            <div class="btns"><button class="btn sm dark">Αποθήκευση</button><button class="btn sm danger" name="action" value="delete_item" formnovalidate>Διαγραφή</button></div></form>
        <?php else: ?>
          <div class="lr<?= $i['available'] ? '' : ' off' ?>">
            <a class="grow" style="color:inherit" href="<?= e(module_url('restaurant-ordering', 'menu?edit=' . $i['id'])) ?>"><b><?= e($i['name']) ?></b><small><?= money($i['price_cents']) ?><?= $i['description'] ? ' · ' . e($i['description']) : '' ?></small></a>
            <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $i['id'] ?>"><button class="btn sm"><?= $i['available'] ? pill('Διαθέσιμο', 'green') : pill('Εξαντλήθηκε', 'grey') ?></button></form>
          </div>
        <?php endif; ?>
      <?php endforeach; ?>
      <form class="lr" method="post" style="display:block"><?= csrf_field() ?><input type="hidden" name="action" value="item"><input type="hidden" name="category_id" value="<?= (int) $c['id'] ?>">
        <div class="inline"><input name="name" placeholder="Νέο πιάτο" required><input name="price" placeholder="€" inputmode="decimal" required style="width:90px"><button class="btn sm dark" style="height:44px">+</button></div></form>
    </div>
  <?php endforeach; ?>
  <?php if (!$categories): ?><div class="card empty">Ο κατάλογος είναι άδειος.</div><?php endif; ?>
</div>
<div>
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="category">
    <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'orange') ?><div><b>Νέα κατηγορία</b></div></div>
    <label>Όνομα</label><input name="name" required placeholder="π.χ. Πίτσες"><button class="btn dark">Προσθήκη</button></form>
  <?php if (!$categories && $hasQrMenu): ?>
    <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="import"><button class="btn">Εισαγωγή από το μενού του QR Boss</button></form>
  <?php endif; ?>
</div>
</div>
