<span class="eyebrow"><?= icon('logo', 12) ?> Πύλη πελάτη</span>
<h1><?= e($biz['name']) ?>. <em>Οργανωμένο.</em></h1>
<p class="lead">Όλα όσα κάνουμε για εσένα (εργαλεία, αιτήματα, ομάδα και πληρωμές) σε ένα μέρος.</p>

<div class="g2 g4">
  <div class="stat"><small>Ενεργά εργαλεία</small><b><?= count($subs) ?></b></div>
  <div class="stat"><small>Ομάδα</small><b><?= $teamCount ?></b></div>
  <div class="stat"><small>Αιτήματα</small><b><?= count($openRequests) ?><?php if ($openRequests): ?> <span class="up">σε εξέλιξη</span><?php endif; ?></b></div>
  <?php if (has_role('owner')): ?>
    <a class="stat" href="<?= e(url('billing')) ?>" style="color:inherit;text-decoration:none"><small>Ανοιχτό υπόλοιπο</small><b><?= money($balance) ?></b></a>
  <?php else: ?>
    <div class="stat"><small>Ανοιχτά θέματα υποστήριξης</small><b><?= $openTickets ?></b></div>
  <?php endif; ?>
</div>

<div class="cols mt">
  <div>
    <?php foreach ($openRequests as $r): ?>
      <div class="card">
        <div class="cardhd"><?= tile('bolt', 'purple') ?><div><b>Αίτημα <?= e($r['tool_name']) ?></b><small><?= e(REQUEST_STATUS[$r['status']][0]) ?> · <?= e(ago($r['created_at'])) ?></small></div></div>
        <?php foreach (request_steps($r['status']) as [$label, $state]): ?>
          <div class="step"><span><?= e($label) ?></span><?= step_pill($state) ?></div>
        <?php endforeach; ?>
      </div>
    <?php endforeach; ?>

    <p class="sechd">Τα εργαλεία σου</p>
    <div class="card lst">
      <?php foreach ($subs as $s): ?>
        <a class="lr" href="<?= e(module_url($s['slug'])) ?>">
          <?= tile($s['icon'], $s['color']) ?>
          <span class="grow"><b><?= e($s['tool_name']) ?></b><small><?= e($s['plan_name']) ?> · ανανέωση <?= e(date_gr($s['renews_on'], false)) ?></small></span>
          <?= icon('chevron') ?>
        </a>
      <?php endforeach; ?>
      <?php if (!$subs): ?>
        <div class="empty">Δεν έχεις ακόμα ενεργά εργαλεία. <a href="<?= e(url('tools')) ?>">Δες τι υπάρχει</a></div>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="cardhd"><?= tile('grid', 'blue') ?><div><b>Ανακάλυψε εργαλεία</b><small>Πρόσθεσε ό,τι χρειάζεσαι, όταν το χρειάζεσαι.</small></div></div>
      <a class="btn" href="<?= e(url('tools')) ?>">Όλα τα εργαλεία <?= icon('arrow', 16) ?></a>
    </div>
    <div class="card">
      <div class="cardhd"><?= tile('headset', 'pink') ?><div><b>Χρειάζεσαι κάτι;</b><small>Απαντάμε την ίδια ημέρα.</small></div></div>
      <a class="btn dark" href="<?= e(url('support')) ?>#new">Νέο αίτημα υποστήριξης</a>
    </div>
  </div>
</div>
