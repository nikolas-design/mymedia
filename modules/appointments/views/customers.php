<?= ap_tabs('customers') ?>
<form method="get" class="inline" style="margin-bottom:12px;max-width:520px"><input name="q" value="<?= e($search) ?>" placeholder="Όνομα, τηλέφωνο ή email"><button class="btn sm auto" style="height:44px">Αναζήτηση</button></form>
<div class="card lst" style="max-width:820px">
  <?php foreach ($customers as $c): ?>
    <a class="lr" href="<?= e(module_url('appointments', 'customers/' . $c['id'])) ?>">
      <span class="av" style="<?= avatar_style($c['name']) ?>"><?= e(initials($c['name'])) ?></span>
      <span class="grow"><b><?= e($c['name']) ?></b><small><?= e($c['phone']) ?> · <?= (int) $c['visits'] ?> ραντεβού<?= $c['last_visit'] ? ' · τελευταίο ' . e(date_gr($c['last_visit'])) : '' ?></small></span>
      <?= $c['noshows'] ? pill($c['noshows'] . '× δεν ήρθε', 'red') : '' ?>
    </a>
  <?php endforeach; ?>
  <?php if (!$customers): ?><div class="empty">Κανένας πελάτης.</div><?php endif; ?>
</div>
