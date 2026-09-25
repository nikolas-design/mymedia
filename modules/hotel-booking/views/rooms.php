<?= hb_tabs('rooms') ?>
<div class="cols">
  <div class="card lst">
    <?php foreach ($rooms as $r): ?>
      <a class="lr<?= $r['active'] ? '' : ' off' ?>" href="<?= e(module_url('hotel-booking', 'rooms/' . $r['id'])) ?>">
        <?php if ($r['photo']): ?><img class="thumb" src="<?= e(upload_url($r['photo'])) ?>" alt=""><?php else: ?><?= tile('building', 'green', 56) ?><?php endif; ?>
        <span class="grow"><b><?= e($r['name']) ?></b><small><?= (int) $r['units'] ?> μονάδες · έως <?= (int) $r['capacity'] ?> άτομα · από <?= money($r['base_price_cents']) ?>/βράδυ</small></span><?= icon('chevron') ?></a>
    <?php endforeach; ?>
    <?php if (!$rooms): ?><div class="empty">Κανένας τύπος δωματίου ακόμα.</div><?php endif; ?>
  </div>
  <div><a class="btn dark" href="<?= e(module_url('hotel-booking', 'rooms/new')) ?>"><?= icon('plus', 16) ?> Νέος τύπος δωματίου</a></div>
</div>
