<?= sh_tabs('week') ?>
<?php $days = sh_week_days($monday); $link = full_url('p/shifts/' . $settings['share_token']); ?>
<div style="display:flex;justify-content:space-between;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:12px">
  <div class="btns" style="align-items:center">
    <a class="btn sm" href="<?= e(module_url('shifts', '?w=' . date('Y-m-d', strtotime("$monday -7 days")))) ?>">‹</a>
    <b><?= e(date_gr($monday, false)) ?> – <?= e(date_gr($days[6])) ?></b>
    <a class="btn sm" href="<?= e(module_url('shifts', '?w=' . date('Y-m-d', strtotime("$monday +7 days")))) ?>">›</a>
    <?= $published ? pill('Δημοσιευμένο', 'green') : pill('Πρόχειρο', 'grey') ?>
  </div>
  <?php if ($canEdit): ?>
    <form method="post" class="btns"><?= csrf_field() ?>
      <button class="btn sm" name="action" value="copy">Αντιγραφή προηγούμενης</button>
      <a class="btn sm" href="<?= e(module_url('shifts', 'print?w=' . $monday)) ?>" target="_blank"><?= icon('print', 14) ?> Εκτύπωση</a>
      <button class="btn sm dark" name="action" value="publish">Δημοσίευση</button>
    </form>
  <?php endif; ?>
</div>

<div class="card scrollx" style="padding:8px">
  <table class="tbl" style="min-width:860px">
    <thead><tr><th>Άτομο</th><?php foreach ($days as $i => $d): ?><th style="<?= $d === date('Y-m-d') ? 'color:var(--p)' : '' ?>"><?= mb_substr(SH_DAYS[$i], 0, 3) ?> <?= e(date('d/m', strtotime($d))) ?></th><?php endforeach; ?><th class="n">Ώρες</th></tr></thead>
    <tbody>
      <?php foreach ($people as $p): $hours = 0; ?>
        <tr>
          <td><b class="small"><?= e($p['name']) ?></b><br><span class="small muted"><?= e($p['position'] ?? '') ?></span></td>
          <?php foreach ($days as $d): ?>
            <td style="vertical-align:top;min-width:92px">
              <?php if (isset($abs[(int) $p['id']][$d])): ?><?= pill(SH_KIND[$abs[(int) $p['id']][$d]], 'orange') ?><?php endif; ?>
              <?php foreach ($grid[(int) $p['id']][$d] ?? [] as $s): $hours += sh_hours($s['start_time'], $s['end_time']); ?>
                <div style="background:var(--pl);color:var(--p);border-radius:8px;padding:4px 6px;font-size:12px;font-weight:600;margin-bottom:4px;display:flex;justify-content:space-between;gap:4px">
                  <span><?= e(sh_time($s['start_time'])) ?>–<?= e(sh_time($s['end_time'])) ?></span>
                  <?php if ($canEdit): ?><form method="post" style="margin:0"><?= csrf_field() ?><input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>"><button class="link" style="color:var(--muted)" title="Διαγραφή">✕</button></form><?php endif; ?>
                </div>
              <?php endforeach; ?>
            </td>
          <?php endforeach; ?>
          <td class="n"><b><?= e(rtrim(rtrim(number_format($hours, 1, ',', ''), '0'), ',')) ?></b><?= $p['weekly_hours'] ? '<br><span class="small muted">/ ' . e(rtrim(rtrim(number_format((float) $p['weekly_hours'], 1, ',', ''), '0'), ',')) . '</span>' : '' ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$people): ?><tr><td colspan="9" class="empty">Πρόσθεσε πρώτα το προσωπικό. <a href="<?= e(module_url('shifts', 'people')) ?>">Προσωπικό</a></td></tr><?php endif; ?>
    </tbody>
  </table>
</div>

<div class="cols">
<?php if ($canEdit && $people): ?>
  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="add">
    <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'blue') ?><div><b>Προσθήκη βάρδιας</b><small>Διάλεξε πολλές ημέρες για την ίδια βάρδια.</small></div></div>
    <label>Άτομο</label><select name="person_id"><?php foreach ($people as $p): ?><option value="<?= (int) $p['id'] ?>"><?= e($p['name']) ?></option><?php endforeach; ?></select>
    <label>Ημέρες</label>
    <div class="btns"><?php foreach ($days as $i => $d): ?><label class="btn sm" style="font-weight:500;gap:4px"><input type="checkbox" name="days[]" value="<?= e($d) ?>"> <?= mb_substr(SH_DAYS[$i], 0, 3) ?></label><?php endforeach; ?></div>
    <div class="row2">
      <div><label>Από</label><input type="time" name="start" value="09:00" required></div>
      <div><label>Έως</label><input type="time" name="end" value="17:00" required></div>
    </div>
    <label>Θέση (προαιρετικό)</label><input name="position" placeholder="π.χ. Μπαρ">
    <button class="btn dark">Προσθήκη</button>
  </form>
<?php endif; ?>
  <div class="card">
    <div class="cardhd" style="margin-bottom:0"><?= tile('users', 'purple') ?><div><b>Σύνδεσμος για την ομάδα</b><small>Όποιος έχει τον σύνδεσμο βλέπει το δημοσιευμένο πρόγραμμα, χωρίς λογαριασμό.</small></div></div>
    <div class="copy"><input id="shlink" value="<?= e($link) ?>" readonly><button class="btn sm" type="button" data-copy="#shlink">Αντιγραφή</button></div>
    <?php if ($canEdit): ?><form method="post" data-confirm="Να σβηστούν όλες οι βάρδιες αυτής της εβδομάδας;"><?= csrf_field() ?><button class="btn sm danger mt" name="action" value="clear">Άδειασμα εβδομάδας</button></form><?php endif; ?>
  </div>
</div>
