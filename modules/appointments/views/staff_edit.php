<?= ap_tabs('staff') ?>
<a class="back" href="<?= e(module_url('appointments', 'staff')) ?>"><?= icon('back', 14) ?> Συνεργάτες</a>
<form method="post" class="cols">
  <?= csrf_field() ?><input type="hidden" name="action" value="save">
  <div>
    <div class="card">
      <div class="row2">
        <div><label style="margin-top:0">Όνομα</label><input name="name" value="<?= e($s['name'] ?? '') ?>" required></div>
        <div><label style="margin-top:0">Χρώμα</label><input type="color" name="color" value="<?= e($s['color'] ?? '#d64b7c') ?>" style="width:64px;padding:4px"></div>
      </div>
      <?php if ($s): ?><label style="display:flex;gap:8px;align-items:center"><input type="checkbox" name="active" value="1" <?= $s['active'] ? 'checked' : '' ?>> Δέχεται ραντεβού</label><?php endif; ?>
      <label>Υπηρεσίες που κάνει</label>
      <div class="btns"><?php foreach ($services as $svc): ?><label class="btn sm" style="font-weight:500;gap:6px"><input type="checkbox" name="services[]" value="<?= (int) $svc['id'] ?>" <?= in_array((int) $svc['id'], $mine, true) ? 'checked' : '' ?>> <?= e($svc['name']) ?></label><?php endforeach; ?></div>
      <?php if (!$services): ?><p class="hint">Δεν υπάρχουν ακόμα υπηρεσίες.</p><?php endif; ?>
    </div>
    <button class="btn dark">Αποθήκευση</button>
  </div>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('calendar', 'blue') ?><div><b>Εβδομαδιαίο ωράριο</b><small>Δεύτερο διάστημα για σπαστό ωράριο. Κενό = κλειστά.</small></div></div>
    <table class="tbl mt"><tbody>
      <?php foreach (AP_DAYS as $d => $label): $h = $hours[$d] ?? []; ?>
        <tr><td class="small"><b><?= e(mb_substr($label, 0, 3)) ?></b></td>
          <?php foreach ([1, 2] as $k): $row = $h[$k - 1] ?? null; ?>
            <td><div class="inline" style="gap:4px"><input type="time" name="h[<?= $d ?>][<?= $k ?>][from]" value="<?= e($row ? substr($row['start_time'], 0, 5) : '') ?>" style="height:36px;padding:0 6px"><input type="time" name="h[<?= $d ?>][<?= $k ?>][to]" value="<?= e($row ? substr($row['end_time'], 0, 5) : '') ?>" style="height:36px;padding:0 6px"></div></td>
          <?php endforeach; ?></tr>
      <?php endforeach; ?>
    </tbody></table>
  </div>
</form>
<?php if ($s): ?>
<div class="cols mt">
  <form class="card" method="post"><?= csrf_field() ?><input type="hidden" name="action" value="timeoff">
    <div class="cardhd" style="margin-bottom:0"><?= tile('calendar', 'orange') ?><div><b>Άδεια / κλειστά</b></div></div>
    <?php foreach ($timeoff as $t): ?><div class="step mt"><span><?= e(date_gr($t['day_from'], false)) ?><?= $t['day_to'] !== $t['day_from'] ? ' – ' . e(date_gr($t['day_to'], false)) : '' ?><?= $t['reason'] ? ' · ' . e($t['reason']) : '' ?></span></div><?php endforeach; ?>
    <div class="row2"><div><label>Από</label><input type="date" name="day_from"></div><div><label>Έως</label><input type="date" name="day_to"></div></div>
    <label>Λόγος</label><input name="reason" placeholder="π.χ. Διακοπές">
    <button class="btn">Προσθήκη</button>
  </form>
  <form method="post" data-confirm="Διαγραφή του συνεργάτη ΚΑΙ των ραντεβού του;"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><button class="btn danger">Διαγραφή συνεργάτη</button></form>
</div>
<?php endif; ?>
