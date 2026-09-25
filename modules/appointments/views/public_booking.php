<?php [$label] = AP_STATUS[$b['status']]; ?>
<?php if (!empty($_GET['new'])): ?><p class="big">🎉</p><?php endif; ?>
<h1><?= match ($b['status']) { 'pending' => 'Λάβαμε το αίτημά σας', 'cancelled' => 'Το ραντεβού ακυρώθηκε', default => 'Το ραντεβού σας' } ?></h1>
<p class="lead"><?= $b['status'] === 'pending' ? 'Θα σας επιβεβαιώσουμε σύντομα.' : e($st['title']) ?></p>
<div class="pcard">
  <div class="list">
    <div class="it"><span class="muted">Υπηρεσία</span><b><?= e($b['service_name']) ?></b></div>
    <div class="it"><span class="muted">Με</span><b><?= e($b['staff']) ?></b></div>
    <div class="it"><span class="muted">Πότε</span><b><?= e(ap_dt($b['starts_at'])) ?></b></div>
    <?php if ($b['price_cents'] !== null): ?><div class="it"><span class="muted">Τιμή</span><b><?= money($b['price_cents']) ?></b></div><?php endif; ?>
    <div class="it"><span class="muted">Κατάσταση</span><b><?= e($label) ?></b></div>
  </div>
</div>
<?php if ($b['status'] !== 'cancelled'): ?>
  <a class="cta ghost" href="data:text/calendar;charset=utf-8,<?= rawurlencode("BEGIN:VCALENDAR\r\nVERSION:2.0\r\nBEGIN:VEVENT\r\nDTSTART:" . gmdate('Ymd\THis\Z', strtotime($b['starts_at'])) . "\r\nDTEND:" . gmdate('Ymd\THis\Z', strtotime($b['ends_at'])) . "\r\nSUMMARY:" . $b['service_name'] . ' · ' . $st['title'] . "\r\nLOCATION:" . ($st['address'] ?? '') . "\r\nEND:VEVENT\r\nEND:VCALENDAR") ?>" download="rantevou.ics">📅 Προσθήκη στο ημερολόγιο</a>
<?php endif; ?>
<?php if ($canCancel): ?>
  <form method="post" onsubmit="return confirm('Σίγουρα ακύρωση;')"><?= csrf_field() ?><button class="cta ghost" name="do" value="cancel">Ακύρωση ραντεβού</button></form>
<?php elseif (in_array($b['status'], ['pending', 'confirmed'], true)): ?>
  <p class="small muted">Για αλλαγή ή ακύρωση τώρα, καλέστε μας<?= $st['phone'] ? ': <a href="tel:' . e($st['phone']) . '">' . e($st['phone']) . '</a>' : '' ?>.</p>
<?php endif; ?>
<a class="cta" href="<?= e(url($base)) ?>" style="margin-top:12px">Νέο ραντεβού</a>
