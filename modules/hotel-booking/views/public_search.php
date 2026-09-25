<style>.pub{text-align:left}.pub h1,.pub>.lead{text-align:center}.room{display:block}.room img{width:100%;border-radius:12px;margin-bottom:10px;max-height:220px;object-fit:cover}</style>
<h1><?= e($st['title']) ?></h1>
<p class="lead">Απευθείας κράτηση, στην καλύτερη τιμή.</p>
<?php if ($error): ?><div class="err"><?= e($error) ?></div><?php endif; ?>
<form method="get" class="pcard">
  <div class="row2"><div><label style="margin-top:0">Άφιξη</label><input type="date" name="in" value="<?= e($in) ?>" min="<?= date('Y-m-d') ?>" required></div><div><label style="margin-top:0">Αναχώρηση</label><input type="date" name="out" value="<?= e($out) ?>" min="<?= date('Y-m-d', strtotime('+1 day')) ?>" required></div></div>
  <label>Άτομα</label><select name="guests"><?php for ($g = 1; $g <= 8; $g++): ?><option <?= $g === $guests ? 'selected' : '' ?>><?= $g ?></option><?php endfor; ?></select>
  <button class="cta">Αναζήτηση διαθεσιμότητας</button>
</form>
<?php if ($in && $out && !$error): $n = hb_nights($in, $out); ?>
  <?php foreach ($offers as $r): ?>
    <details class="pcard room" <?= (int) ($_POST['room_id'] ?? 0) === (int) $r['id'] ? 'open' : '' ?>>
      <summary style="list-style:none;cursor:pointer">
        <?php if ($r['photo']): ?><img src="<?= e(upload_url($r['photo'])) ?>" alt=""><?php endif; ?>
        <div style="display:flex;justify-content:space-between;gap:10px"><div><b><?= e($r['name']) ?></b><br><span class="small muted">Έως <?= (int) $r['capacity'] ?> άτομα</span></div>
          <div style="text-align:right"><b style="font-size:20px;color:var(--brand)"><?= money($r['total']) ?></b><br><span class="small muted"><?= $n ?> βράδια</span></div></div>
        <?php if ($r['description']): ?><p class="small muted"><?= e($r['description']) ?></p><?php endif; ?>
        <span class="cta" style="margin-top:8px">Κράτηση</span>
      </summary>
      <form method="post" action="<?= e(url($base) . '?' . http_build_query(['in' => $in, 'out' => $out, 'guests' => $guests])) ?>"><?= csrf_field() ?><input type="hidden" name="room_id" value="<?= (int) $r['id'] ?>">
        <label>Ονοματεπώνυμο</label><input name="name" required autocomplete="name">
        <div class="row2"><div><label>Email</label><input name="email" type="email" required autocomplete="email"></div><div><label>Τηλέφωνο</label><input name="phone" type="tel" required autocomplete="tel"></div></div>
        <label>Χώρα</label><input name="country" autocomplete="country-name">
        <label>Σχόλια (ώρα άφιξης, ειδικές ανάγκες)</label><input name="notes">
        <?php if ($st['policy']): ?><p class="small muted" style="white-space:pre-wrap"><?= e($st['policy']) ?></p><?php endif; ?>
        <button class="cta"><?= $st['auto_confirm'] ? 'Ολοκλήρωση κράτησης' : 'Αποστολή αιτήματος κράτησης' ?></button>
      </form>
    </details>
  <?php endforeach; ?>
  <?php if (!$offers): ?><div class="err">Δεν υπάρχει διαθεσιμότητα για αυτές τις ημερομηνίες. Δοκιμάστε άλλες ή καλέστε μας<?= $st['phone'] ? ' στο ' . e($st['phone']) : '' ?>.</div><?php endif; ?>
<?php endif; ?>
<p class="small muted" style="text-align:center">Check-in από <?= e($st['checkin']) ?> · Check-out έως <?= e($st['checkout']) ?><?= $st['address'] ? ' · ' . e($st['address']) : '' ?></p>
