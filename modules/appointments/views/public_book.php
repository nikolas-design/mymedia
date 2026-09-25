<?php $q = fn(array $p) => url($base) . '?' . http_build_query(array_filter($p + ['service' => $service['id'] ?? null, 'staff' => $staffId ?: null, 'day' => $day], fn($v) => $v !== null)); ?>
<h1><?= e($st['title']) ?></h1>
<p class="lead"><?= e($st['intro'] ?: 'Κλείστε το ραντεβού σας online.') ?></p>
<?php if (!empty($error)): ?><div class="err"><?= e($error) ?></div><?php endif; ?>

<div class="pcard">
  <b>1. Υπηρεσία</b>
  <div class="list">
    <?php foreach ($services as $s): $on = $service && (int) $s['id'] === (int) $service['id']; ?>
      <a class="it" href="<?= e(url($base) . '?service=' . $s['id']) ?>" style="text-decoration:none;color:inherit;<?= $on ? 'font-weight:700;color:var(--brand)' : '' ?>">
        <span><?= $on ? '✓ ' : '' ?><?= e($s['name']) ?><br><span class="small muted"><?= (int) $s['duration_min'] ?>'<?= $s['description'] ? ' · ' . e($s['description']) : '' ?></span></span>
        <span><?= $s['price_cents'] !== null ? money($s['price_cents']) : '' ?></span></a>
    <?php endforeach; ?>
    <?php if (!$services): ?><p class="muted">Δεν υπάρχουν διαθέσιμες υπηρεσίες αυτή τη στιγμή.</p><?php endif; ?>
  </div>
</div>

<?php if ($service): ?>
  <?php if (count($staffList) > 1): ?>
  <div class="pcard"><b>2. Με ποιον;</b><div style="margin-top:8px">
    <a class="slot" style="text-decoration:none;<?= !$staffId ? 'background:var(--brand);color:#fff' : 'color:inherit' ?>" href="<?= e($q(['staff' => null])) ?>">Οποιοσδήποτε</a>
    <?php foreach ($staffList as $f): ?><a class="slot" style="text-decoration:none;<?= $staffId === (int) $f['id'] ? 'background:var(--brand);color:#fff' : 'color:inherit' ?>" href="<?= e($q(['staff' => $f['id']])) ?>"><?= e($f['name']) ?></a><?php endforeach; ?>
  </div></div>
  <?php endif; ?>

  <div class="pcard"><b><?= count($staffList) > 1 ? '3' : '2' ?>. Ημέρα</b><div style="margin-top:8px">
    <?php foreach ($days ?? [] as $d => $n): $wd = ['', 'Δευ', 'Τρί', 'Τετ', 'Πέμ', 'Παρ', 'Σάβ', 'Κυρ'][(int) date('N', strtotime($d))]; ?>
      <?php if ($n): ?><a class="slot" style="text-decoration:none;<?= $day === $d ? 'background:var(--brand);color:#fff' : 'color:inherit' ?>" href="<?= e($q(['day' => $d])) ?>"><?= $wd ?> <?= date('d/m', strtotime($d)) ?></a>
      <?php else: ?><span class="slot" style="opacity:.35;cursor:default"><?= $wd ?> <?= date('d/m', strtotime($d)) ?></span><?php endif; ?>
    <?php endforeach; ?>
    <form method="get" style="margin-top:8px"><input type="hidden" name="service" value="<?= (int) $service['id'] ?>"><?php if ($staffId): ?><input type="hidden" name="staff" value="<?= $staffId ?>"><?php endif; ?>
      <input type="date" name="day" value="<?= e((string) $day) ?>" min="<?= date('Y-m-d') ?>" max="<?= e($lastDay) ?>" onchange="this.form.submit()"></form>
  </div></div>

  <?php if ($day): ?>
    <form method="post" class="pcard" action="<?= e($q([])) ?>">
      <?= csrf_field() ?>
      <b><?= count($staffList) > 1 ? '4' : '3' ?>. Ώρα</b>
      <div style="margin-top:8px">
        <?php foreach ($free as $t => $sid): ?><label class="slot"><input type="radio" name="time" value="<?= e($t) ?>" required><?= e($t) ?></label><?php endforeach; ?>
        <?php if (!$free): ?><p class="muted">Δεν υπάρχουν ελεύθερες ώρες αυτή την ημέρα.</p><?php endif; ?>
      </div>
      <?php if ($free): ?>
        <label>Ονοματεπώνυμο</label><input name="name" required autocomplete="name" value="<?= e(input('name')) ?>">
        <label>Κινητό</label><input name="phone" type="tel" required autocomplete="tel" value="<?= e(input('phone')) ?>">
        <label>Email (για επιβεβαίωση και υπενθύμιση)</label><input name="email" type="email" autocomplete="email" value="<?= e(input('email')) ?>">
        <label>Σχόλια (προαιρετικό)</label><input name="notes">
        <button class="cta">Κλείσιμο ραντεβού</button>
      <?php endif; ?>
    </form>
  <?php endif; ?>
<?php endif; ?>
<?php if ($st['phone'] || $st['address']): ?><p class="small muted"><?= e($st['address'] ?? '') ?><?= $st['phone'] ? ' · <a href="tel:' . e($st['phone']) . '">' . e($st['phone']) . '</a>' : '' ?></p><?php endif; ?>
