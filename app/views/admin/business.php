<?php $monthly = array_sum(array_map('monthly_cents', $subs)); ?>
<a class="back" href="<?= e(url('admin/businesses')) ?>"><?= icon('back', 14) ?> Επιχειρήσεις</a>
<h1><?= e($b['name']) ?></h1>
<p class="lead"><?= count($subs) ?> εργαλεία · <?= money($monthly) ?>/μήνα · πελάτης από <?= e(date_gr($b['created_at'])) ?></p>

<?php if ($inviteLink): ?>
  <div class="card">
    <div class="cardhd"><?= tile('mail', 'green') ?><div><b>Σύνδεσμος πρόσκλησης</b><small>Στάλθηκε με email. Μπορείς να τον στείλεις και αλλιώς.</small></div></div>
    <div class="copy"><input id="invlink" value="<?= e($inviteLink) ?>" readonly><button class="btn sm" type="button" data-copy="#invlink">Αντιγραφή</button></div>
  </div>
<?php endif; ?>

<div class="cols">
<div>
  <p class="sechd" style="margin-top:0">Συνδρομές</p>
  <div class="card lst">
    <?php foreach ($subs as $s): ?>
      <details class="lr" style="display:block">
        <summary style="display:flex;align-items:center;gap:12px;cursor:pointer;list-style:none">
          <?= tile($s['icon'], $s['color']) ?>
          <span class="grow"><b><?= e($s['tool_name']) ?> · <?= e($s['plan_name']) ?></b><small><?= money($s['price_cents']) ?><?= period_label($s['billing']) ?> · ανανέωση <?= e(date_gr($s['renews_on'])) ?></small></span>
          <?= icon('chevron') ?>
        </summary>
        <form method="post" class="mt">
          <?= csrf_field() ?><input type="hidden" name="action" value="edit_sub"><input type="hidden" name="id" value="<?= (int) $s['id'] ?>">
          <div class="row2">
            <div><label>Τιμή (χωρίς ΦΠΑ)</label><input name="price" value="<?= e(number_format($s['price_cents'] / 100, 2, ',', '')) ?>" inputmode="decimal"></div>
            <div><label>Επόμενη ανανέωση</label><input name="renews_on" type="date" value="<?= e($s['renews_on']) ?>"></div>
          </div>
          <div class="btns"><button class="btn sm dark">Αποθήκευση</button>
            <button class="btn sm danger" name="cancel" value="1" onclick="return confirm('Ακύρωση της συνδρομής;')">Ακύρωση συνδρομής</button></div>
        </form>
      </details>
    <?php endforeach; ?>
    <?php if (!$subs): ?><div class="empty">Καμία ενεργή συνδρομή.</div><?php endif; ?>
  </div>

  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="add_sub">
    <div class="cardhd" style="margin-bottom:0"><?= tile('plus', 'purple') ?><div><b>Προσθήκη συνδρομής</b></div></div>
    <label>Πλάνο</label>
    <select name="plan_id" required><option value="">Διάλεξε…</option>
      <?php foreach ($plans as $p): ?><option value="<?= (int) $p['id'] ?>"><?= e($p['tool_name'] . ' · ' . $p['name'] . ' · ' . money($p['price_cents']) . period_label($p['period'])) ?></option><?php endforeach; ?>
    </select>
    <div class="row2">
      <div><label>Χρέωση</label><select name="billing"><option value="month">Μηνιαία</option><option value="year">Ετήσια (x10)</option></select></div>
      <div><label>Έναρξη</label><input type="date" name="started_on" value="<?= date('Y-m-d') ?>"></div>
    </div>
    <button class="btn">Προσθήκη</button>
  </form>

  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="invoice">
    <div class="cardhd" style="margin-bottom:0"><?= tile('receipt', 'orange') ?><div><b>Έκδοση παραστατικού</b><small>Οι επιλεγμένες συνδρομές μεταφέρουν την ανανέωσή τους μία περίοδο μετά.</small></div></div>
    <?php foreach ($subs as $s): ?>
      <label style="display:flex;gap:8px;align-items:center;font-weight:400"><input type="checkbox" name="subs[]" value="<?= (int) $s['id'] ?>" <?= $s['renews_on'] <= date('Y-m-d', strtotime('+7 days')) ? 'checked' : '' ?>>
        <?= e($s['tool_name'] . ' · ' . $s['plan_name']) ?> · <?= money($s['price_cents']) ?> <span class="muted small">(ανανέωση <?= e(date_gr($s['renews_on'], false)) ?>)</span></label>
    <?php endforeach; ?>
    <div class="row2">
      <div><label>Επιπλέον γραμμή</label><input name="extra_desc" placeholder="π.χ. Εκτύπωση QR"></div>
      <div><label>Ποσό (χωρίς ΦΠΑ)</label><input name="extra_amount" inputmode="decimal" placeholder="0,00"></div>
    </div>
    <label>Ημερομηνία έκδοσης</label><input type="date" name="issued_on" value="<?= date('Y-m-d') ?>">
    <button class="btn dark">Έκδοση (+ΦΠΑ <?= VAT_RATE ?>%)</button>
  </form>

  <p class="sechd">Παραστατικά</p>
  <div class="card lst">
    <?php foreach ($invoices as $i): ?>
      <a class="lr" href="<?= e(url('invoices/' . $i['id'])) ?>">
        <span class="grow"><b><?= e($i['number']) ?></b><small><?= e(date_gr($i['issued_on'])) ?></small></span>
        <span class="r"><b><?= money($i['total_cents']) ?></b><?= invoice_pill($i['status'], $i['due_on']) ?></span>
      </a>
    <?php endforeach; ?>
    <?php if (!$invoices): ?><div class="empty">Κανένα παραστατικό.</div><?php endif; ?>
  </div>
</div>

<div>
  <p class="sechd" style="margin-top:0">Ομάδα</p>
  <div class="card lst">
    <?php foreach ($members as $m): ?>
      <form class="lr" method="post">
        <?= csrf_field() ?><input type="hidden" name="action" value="member"><input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
        <span class="av" style="<?= avatar_style($m['name']) ?>"><?= e(initials($m['name'])) ?></span>
        <span class="grow"><b><?= e($m['name']) ?></b><small><?= e($m['email']) ?> · σύνδεση <?= e(ago($m['last_login_at'])) ?></small></span>
        <select name="role" style="width:auto;height:34px;font-size:13px"><?php foreach (ROLE_LABELS as $k => $v): ?><option value="<?= $k ?>" <?= $m['role'] === $k ? 'selected' : '' ?>><?= e($v) ?></option><?php endforeach; ?></select>
        <label style="margin:0" title="Ενεργό"><input type="checkbox" name="active" value="1" <?= $m['active'] ? 'checked' : '' ?>></label>
        <button class="btn sm">OK</button>
      </form>
    <?php endforeach; ?>
    <?php foreach ($invites as $i): ?>
      <div class="lr"><?= tile('mail', 'grey') ?><span class="grow"><b><?= e($i['email']) ?></b><small>Πρόσκληση · <?= e(ROLE_LABELS[$i['role']]) ?> · λήγει <?= e(date_gr($i['expires_at'], false)) ?></small></span></div>
    <?php endforeach; ?>
    <?php if (!$members && !$invites): ?><div class="empty">Κανένα μέλος.</div><?php endif; ?>
  </div>
  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="invite">
    <div class="row2">
      <div><label style="margin-top:0">Πρόσκληση (email)</label><input name="email" type="email" required></div>
      <div><label style="margin-top:0">Ρόλος</label><select name="role"><option value="owner">Ιδιοκτήτης</option><option value="manager">Υπεύθυνος</option><option value="member">Μέλος</option></select></div>
    </div>
    <button class="btn">Αποστολή πρόσκλησης</button>
  </form>

  <?php if ($requests): ?>
    <p class="sechd">Αιτήματα</p>
    <div class="card lst">
      <?php foreach ($requests as $r): ?>
        <div class="lr"><span class="grow"><b><?= e($r['tool_name']) ?></b><small><?= e(ago($r['created_at'])) ?></small></span><?= request_pill($r['status']) ?></div>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <form class="card" method="post">
    <?= csrf_field() ?><input type="hidden" name="action" value="info">
    <div class="cardhd" style="margin-bottom:0"><?= tile('building', 'blue') ?><div><b>Στοιχεία επιχείρησης</b></div></div>
    <label>Όνομα</label><input name="name" value="<?= e($b['name']) ?>" required>
    <label>Επωνυμία</label><input name="legal_name" value="<?= e($b['legal_name']) ?>">
    <div class="row2">
      <div><label>ΑΦΜ</label><input name="vat_number" value="<?= e($b['vat_number']) ?>"></div>
      <div><label>ΔΟΥ</label><input name="tax_office" value="<?= e($b['tax_office']) ?>"></div>
    </div>
    <label>Διεύθυνση</label><input name="address" value="<?= e($b['address']) ?>">
    <div class="row2">
      <div><label>Τηλέφωνο</label><input name="phone" value="<?= e($b['phone']) ?>"></div>
      <div><label>Email τιμολόγησης</label><input name="billing_email" type="email" value="<?= e($b['billing_email']) ?>"></div>
    </div>
    <button class="btn">Αποθήκευση</button>
  </form>

  <details class="card">
    <summary style="cursor:pointer;color:var(--bad);font-weight:600">Διαγραφή επιχείρησης</summary>
    <form method="post" class="mt">
      <?= csrf_field() ?><input type="hidden" name="action" value="delete">
      <p class="small">Σβήνει οριστικά την επιχείρηση, τις συνδρομές, τα παραστατικά και τα αιτήματά της. Γράψε <b><?= e($b['name']) ?></b> για επιβεβαίωση.</p>
      <input name="confirm_name" autocomplete="off" required>
      <button class="btn danger">Οριστική διαγραφή</button>
    </form>
  </details>
</div>
</div>
