<?php
/** @var string $content @var string $title @var string $nav @var string $area */
$appName = $GLOBALS['config']['app_name'] ?? 'MyMedia';
$user = current_user();
$biz = $user ? current_business() : null;
$flashes = take_flashes();

$unread = 0;
$adminCounts = ['requests' => 0, 'tickets' => 0];
if ($user && $area !== 'auth') {
    if ($biz) {
        $unread = (int) qval('SELECT COUNT(*) FROM notifications WHERE business_id = ? AND read_at IS NULL', [$biz['id']]);
    }
    if (is_admin()) {
        $adminCounts['requests'] = (int) qval("SELECT COUNT(*) FROM tool_requests WHERE status IN ('new','setup')");
        $adminCounts['tickets'] = (int) qval("SELECT COUNT(*) FROM tickets WHERE status = 'open'");
    }
}

// [κλειδί, διαδρομή, ετικέτα, εικονίδιο, μετρητής]
$clientNav = [];
if ($biz) {
    $clientNav[] = ['dashboard', 'dashboard', 'Αρχική', 'home', 0];
    $clientNav[] = ['tools', 'tools', 'Εργαλεία', 'grid', 0];
    $clientNav[] = ['team', 'team', 'Ομάδα', 'users', 0];
    if (has_role('owner')) {
        $clientNav[] = ['billing', 'billing', 'Χρεώσεις', 'receipt', 0];
    }
    $clientNav[] = ['support', 'support', 'Υποστήριξη', 'headset', 0];
    $clientNav[] = ['profile', 'profile', 'Προφίλ', 'user', 0];
    if (has_role('owner', 'manager')) {
        $clientNav[] = ['business', 'business', 'Ρυθμίσεις επιχείρησης', 'settings', 0];
    }
    $clientNav[] = ['notifications', 'notifications', 'Ειδοποιήσεις', 'bell', $unread];
}
$adminNav = [
    ['admin', 'admin', 'Επισκόπηση', 'chart', 0],
    ['admin-requests', 'admin/requests', 'Αιτήματα', 'bolt', $adminCounts['requests']],
    ['admin-businesses', 'admin/businesses', 'Επιχειρήσεις', 'building', 0],
    ['admin-invoices', 'admin/invoices', 'Παραστατικά', 'receipt', 0],
    ['admin-tickets', 'admin/tickets', 'Υποστήριξη', 'headset', $adminCounts['tickets']],
    ['admin-tools', 'admin/tools', 'Εργαλεία & τιμές', 'grid', 0],
    ['admin-settings', 'admin/settings', 'Ρυθμίσεις', 'settings', 0],
];

if ($area === 'admin') {
    $bottom = [
        ['admin', 'admin', 'Επισκόπηση', 'chart'],
        ['admin-requests', 'admin/requests', 'Αιτήματα', 'bolt'],
        ['admin-businesses', 'admin/businesses', 'Πελάτες', 'building'],
        ['admin-invoices', 'admin/invoices', 'Παραστατικά', 'receipt'],
        ['profile', 'profile', 'Μενού', 'menu'],
    ];
} else {
    $bottom = [
        ['dashboard', 'dashboard', 'Αρχική', 'home'],
        ['tools', 'tools', 'Εργαλεία', 'grid'],
        ['team', 'team', 'Ομάδα', 'users'],
        has_role('owner') ? ['billing', 'billing', 'Χρεώσεις', 'receipt'] : ['support', 'support', 'Υποστήριξη', 'headset'],
        ['profile', 'profile', 'Μενού', 'menu'],
    ];
}

function nav_link(array $item, string $current): string
{
    [$key, $path, $label, $ico, $count] = $item + [4 => 0];
    return '<a href="' . e(url($path)) . '" class="' . ($key === $current ? 'on' : '') . '">'
        . icon($ico, 18) . '<span>' . e($label) . '</span>'
        . ($count ? '<span class="cnt">' . (int) $count . '</span>' : '') . '</a>';
}
?><!doctype html>
<html lang="el">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width,initial-scale=1,viewport-fit=cover">
<meta name="theme-color" content="#ffffff">
<title><?= e(($title ? $title . ' · ' : '') . $appName) ?></title>
<link rel="icon" href="<?= e(asset('icon.svg')) ?>" type="image/svg+xml">
<link rel="stylesheet" href="<?= e(asset('app.css')) ?>">
</head>
<body>
<?php if ($area === 'auth'): ?>
<div class="auth">
  <header class="hd">
    <a class="brand" href="<?= e(url()) ?>"><?= tile('logo', 'purple', 28) ?> <?= e($appName) ?></a>
  </header>
  <div class="wrap narrow">
    <?php foreach ($flashes as $f): ?><div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endforeach; ?>
    <?= $content ?>
  </div>
</div>
<?php else: ?>
<div class="shell">
  <aside class="side">
    <a class="brand" href="<?= e(url()) ?>"><?= tile('logo', 'purple', 28) ?> <?= e($appName) ?></a>
    <nav class="snav">
      <?php if ($area === 'admin'): ?>
        <?php foreach ($adminNav as $item) echo nav_link($item, $nav); ?>
        <?php if ($biz): ?>
          <div class="sec">Πύλη πελάτη</div>
          <?= nav_link(['dashboard', 'dashboard', $biz['name'], 'home'], $nav) ?>
        <?php endif; ?>
      <?php else: ?>
        <?php foreach ($clientNav as $item) echo nav_link($item, $nav); ?>
        <?php if (is_admin()): ?>
          <div class="sec">Πλατφόρμα</div>
          <?= nav_link(['admin', 'admin', 'Διαχείριση', 'shield', $adminCounts['requests'] + $adminCounts['tickets']], $nav) ?>
        <?php endif; ?>
      <?php endif; ?>
    </nav>
    <?php if ($user): ?>
    <div class="who">
      <b><?= e($user['name']) ?></b>
      <?= $area === 'admin' ? 'Διαχειριστής πλατφόρμας' : e($biz['name'] ?? '') ?>
      <form method="post" action="<?= e(url('logout')) ?>" class="mt"><?= csrf_field() ?><button class="link" type="submit">Αποσύνδεση</button></form>
    </div>
    <?php endif; ?>
  </aside>

  <div class="main">
    <header class="hd mob">
      <a class="brand" href="<?= e(url()) ?>"><?= tile('logo', 'purple', 28) ?> <?= e($appName) ?></a>
      <div class="hdr">
        <?php if ($area !== 'admin' && $biz): ?><span class="bizname"><?= e($biz['name']) ?></span><?php endif; ?>
        <?php if ($area === 'admin'): ?><span class="bizname">Admin</span><?php endif; ?>
        <?php if ($biz): ?>
          <a class="ib" href="<?= e(url('notifications')) ?>" aria-label="Ειδοποιήσεις"><?= icon('bell', 18) ?><?php if ($unread): ?><span class="dot"><?= $unread ?></span><?php endif; ?></a>
        <?php endif; ?>
      </div>
    </header>
    <div class="wrap">
      <?php foreach ($flashes as $f): ?><div class="flash <?= e($f['type']) ?>"><?= e($f['msg']) ?></div><?php endforeach; ?>
      <?= $content ?>
    </div>
  </div>

  <?php if ($user): ?>
  <nav class="bn">
    <?php foreach ($bottom as [$key, $path, $label, $ico]): ?>
      <a href="<?= e(url($path)) ?>" class="<?= $key === $nav ? 'on' : '' ?>"><?= icon($ico, 22) ?><span><?= e($label) ?></span></a>
    <?php endforeach; ?>
  </nav>
  <?php endif; ?>
</div>
<?php endif; ?>
<script src="<?= e(asset('app.js')) ?>" defer></script>
</body>
</html>
