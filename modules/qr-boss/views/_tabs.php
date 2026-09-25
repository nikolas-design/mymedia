<?php
$openCalls = $pro ? (int) qval("SELECT COUNT(*) FROM qr_calls WHERE business_id = ? AND status = 'new'", [$business['id']]) : 0;
$qrTabs = [
    ['overview', '', 'Επισκόπηση'],
    ['codes', 'codes', 'Κωδικοί QR'],
    ['menu', 'menu', 'Μενού'],
];
if ($pro) {
    $qrTabs[] = ['calls', 'calls', 'Κλήσεις'];
}
$qrTabs[] = ['settings', 'settings', 'Εμφάνιση'];
?>
<div class="toolhd"><?= tile('qr', 'purple', 44) ?><div><h1>QR Boss</h1><span class="small muted"><?= e($subscription['plan_name']) ?> · <?= e($business['name']) ?></span></div></div>
<nav class="tabs">
  <?php foreach ($qrTabs as [$key, $path, $label]): ?>
    <a href="<?= e(module_url('qr-boss', $path)) ?>" class="<?= $tab === $key ? 'on' : '' ?>"><?= e($label) ?><?php if ($key === 'calls' && $openCalls): ?> <span class="cnt"><?= $openCalls ?></span><?php endif; ?></a>
  <?php endforeach; ?>
  <a href="<?= e(module_url('qr-boss', 'preview')) ?>" target="_blank" rel="noopener"><?= icon('play', 12) ?> Προβολή μενού</a>
</nav>
