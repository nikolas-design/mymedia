<?php require __DIR__ . '/_tabs.php'; ?>
<?php
$move = function (string $what, int $id, string $dir) {
    return '<form method="post" style="display:inline">' . csrf_field()
        . '<input type="hidden" name="action" value="move"><input type="hidden" name="what" value="' . $what . '">'
        . '<input type="hidden" name="id" value="' . $id . '"><input type="hidden" name="dir" value="' . $dir . '">'
        . '<button class="btn sm" style="padding:0 8px" title="' . ($dir === 'up' ? 'Πάνω' : 'Κάτω') . '">' . ($dir === 'up' ? '↑' : '↓') . '</button></form>';
};
?>
<div class="cols">
<div>
  <?php foreach ($categories as $cat): $list = $items[(int) $cat['id']] ?? []; ?>
    <div style="display:flex;align-items:center;gap:6px;margin:22px 0 10px">
      <p class="sechd" style="margin:0;flex:1"><?= e($cat['name']) ?><?= $cat['active'] ? '' : ' · κρυφή' ?> <span class="muted">(<?= count($list) ?>)</span></p>
      <?php if ($canEdit): ?>
        <?= $move('category', (int) $cat['id'], 'up') ?><?= $move('category', (int) $cat['id'], 'down') ?>
        <a class="btn sm" href="<?= e(module_url('qr-boss', 'menu/category/' . $cat['id'])) ?>">Αλλαγή</a>
        <a class="btn sm dark" href="<?= e(module_url('qr-boss', 'menu/item/new?cat=' . $cat['id'])) ?>"><?= icon('plus', 14) ?> Πιάτο</a>
      <?php endif; ?>
    </div>
    <div class="card lst<?= $cat['active'] ? '' : ' off' ?>">
      <?php foreach ($list as $i): ?>
        <div class="lr<?= $i['available'] ? '' : ' off' ?>" id="i<?= (int) $i['id'] ?>">
          <?php if ($i['photo']): ?><img class="thumb" src="<?= e(upload_url($i['photo'])) ?>" alt="" loading="lazy"><?php else: ?><?= tile('utensils', 'grey', 56) ?><?php endif; ?>
          <a class="grow" style="color:inherit;min-width:0" href="<?= e($canEdit ? module_url('qr-boss', 'menu/item/' . $i['id']) : '#') ?>">
            <b><?= e($i['name']) ?></b>
            <small><?= $i['price_cents'] !== null ? money($i['price_cents']) : '—' ?><?= $i['description'] ? ' · ' . e($i['description']) : '' ?></small>
          </a>
          <?php if ($canEdit): ?><?= $move('item', (int) $i['id'], 'up') ?><?php endif; ?>
          <form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int) $i['id'] ?>">
            <button class="btn sm" title="Αλλαγή διαθεσιμότητας"><?= $i['available'] ? pill('Διαθέσιμο', 'green') : pill('Εξαντλήθηκε', 'grey') ?></button></form>
        </div>
      <?php endforeach; ?>
      <?php if (!$list): ?><div class="empty">Κανένα πιάτο σε αυτή την κατηγορία.</div><?php endif; ?>
    </div>
  <?php endforeach; ?>
  <?php if (!$categories): ?>
    <div class="card c" style="padding:28px 16px"><?= tile('utensils', 'orange', 56) ?>
      <h1 style="font-size:22px">Ξεκίνα το μενού σου</h1>
      <p class="lead">Πρόσθεσε πρώτα κατηγορίες (π.χ. Καφέδες, Σαλάτες, Κυρίως) και μετά τα πιάτα τους.</p>
      <?php if ($canEdit): ?><form method="post"><?= csrf_field() ?><input type="hidden" name="action" value="sample"><button class="btn auto">Ή φόρτωσε δείγμα μενού για να δεις πώς φαίνεται</button></form><?php endif; ?></div>
  <?php endif; ?>
</div>
<div>
  <?php if ($canEdit): ?>
    <form class="card" method="post">
      <?= csrf_field() ?><input type="hidden" name="action" value="category">
      <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'purple') ?><div><b>Νέα κατηγορία</b></div></div>
      <label>Όνομα</label><input name="name" required placeholder="π.χ. Καφέδες">
      <button class="btn dark">Προσθήκη</button>
    </form>
  <?php endif; ?>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('bolt', 'green') ?><div><b>Τελείωσε κάτι;</b><small>Πάτα «Διαθέσιμο» για να φαίνεται ως «Εξαντλήθηκε» στο μενού. Το μπορεί όλη η ομάδα.</small></div></div>
  </div>
  <a class="btn" href="<?= e(module_url('qr-boss', 'preview')) ?>" target="_blank" rel="noopener"><?= icon('play', 14) ?> Δες το μενού όπως οι πελάτες</a>
</div>
</div>
