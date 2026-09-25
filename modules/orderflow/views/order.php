<?= of_tabs('orders') ?>
<a class="back" href="<?= e(module_url('orderflow', 'orders')) ?>"><?= icon('back', 14) ?> Ιστορικό</a>
<div class="cols">
<div>
  <div class="card">
    <div class="cardhd"><span class="av" style="<?= avatar_style($o['supplier']) ?>"><?= e(initials($o['supplier'])) ?></span>
      <div><b><?= e($o['supplier']) ?> · #<?= (int) $o['id'] ?></b><small><?= e(ago($o['created_at'])) ?><?= $o['by_name'] ? ' · ' . e($o['by_name']) : '' ?></small></div>
      <span style="margin-left:auto"><?= pill(...OF_STATUS[$o['status']]) ?></span></div>
    <table class="tbl"><tbody>
      <?php foreach ($lines as $l): ?><tr><td><?= e($l['name']) ?></td><td class="n"><?= e(of_qty($l['qty'])) ?> <?= e($l['unit']) ?></td><td class="n muted"><?= $l['price_cents'] !== null ? money((int) round($l['qty'] * $l['price_cents'])) : '' ?></td></tr><?php endforeach; ?>
      <?php if ($o['total_cents']): ?><tr><td><b>Εκτίμηση</b></td><td></td><td class="n"><b><?= money($o['total_cents']) ?></b></td></tr><?php endif; ?>
    </tbody></table>
    <?php if ($o['delivery_date']): ?><p class="small mt mb0">Παράδοση: <b><?= e(date_gr($o['delivery_date'])) ?></b></p><?php endif; ?>
    <?php if ($o['note']): ?><p class="small mb0">Σημείωση: <?= e($o['note']) ?></p><?php endif; ?>
  </div>
  <a class="btn" href="<?= e(module_url('orderflow', 'new/' . $o['supplier_id'] . '?from=' . $o['id'])) ?>">↻ Επανάληψη παραγγελίας</a>
</div>
<div>
  <?php if ($o['status'] === 'draft'): ?>
    <div class="card">
      <div class="cardhd" style="margin-bottom:0"><?= tile('arrow', 'purple') ?><div><b>Αποστολή στον προμηθευτή</b><small>Διάλεξε τρόπο. Η παραγγελία σημειώνεται ως σταλμένη.</small></div></div>
      <textarea id="otext" rows="8" class="mt mono" readonly><?= e($text) ?></textarea>
      <form method="post" class="btns mt"><?= csrf_field() ?>
        <?php if ($o['email']): ?><button class="btn dark" name="do" value="email"><?= icon('mail', 16) ?> Email</button><?php endif; ?>
        <?php if ($phone): ?>
          <a class="btn" href="viber://forward?text=<?= rawurlencode($text) ?>" data-mark="viber">Viber</a>
          <a class="btn" href="https://wa.me/<?= e($phone) ?>?text=<?= rawurlencode($text) ?>" target="_blank" rel="noopener" data-mark="whatsapp">WhatsApp</a>
        <?php endif; ?>
        <button class="btn" type="button" data-copy="#otext">Αντιγραφή</button>
      </form>
      <form method="post" class="btns"><?= csrf_field() ?>
        <button class="btn sm" name="do" value="other">Την έστειλα αλλιώς</button>
        <button class="btn sm danger" name="do" value="delete">Διαγραφή</button>
      </form>
    </div>
  <?php elseif ($o['status'] === 'sent'): ?>
    <form class="card" method="post"><?= csrf_field() ?>
      <div class="cardhd" style="margin-bottom:0"><?= tile('truck', 'indigo') ?><div><b>Ήρθε η παραγγελία;</b><small>Στάλθηκε <?= e(ago($o['sent_at'])) ?><?= $o['sent_via'] ? ' μέσω ' . e($o['sent_via']) : '' ?>.</small></div></div>
      <button class="btn dark" name="do" value="received">✓ Παραλήφθηκε</button>
      <button class="btn danger" name="do" value="cancel">Ακύρωση</button>
    </form>
  <?php endif; ?>
</div>
</div>
<script>
// Viber/WhatsApp: μετά το άνοιγμα, σημείωσε την παραγγελία ως σταλμένη
document.querySelectorAll('[data-mark]').forEach(function (a) {
  a.addEventListener('click', function () {
    var f = a.closest('form'), i = document.createElement('input');
    i.type = 'hidden'; i.name = 'do'; i.value = a.dataset.mark; f.appendChild(i);
    setTimeout(function () { f.submit(); }, 800);
  });
});
</script>
