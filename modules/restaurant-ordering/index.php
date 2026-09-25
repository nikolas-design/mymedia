<?php
declare(strict_types=1);

// Restaurant Ordering μέσα στην πύλη
require __DIR__ . '/lib.php';

$settings = ro_settings($bid);
$routes = [
    ['',                'orders'],
    ['feed',            'feed'],
    ['orders/(\d+)',    'order'],
    ['history',         'history'],
    ['menu',            'menu'],
    ['settings',        'settings'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function ro_tabs(string $current): string
{
    $ctx = $GLOBALS['module_ctx'];
    $new = (int) qval("SELECT COUNT(*) FROM ro_orders WHERE business_id = ? AND status = 'new'", [$ctx['bid']]);
    $tabs = [['orders', '', 'Παραγγελίες', $new], ['history', 'history', 'Ιστορικό']];
    if ($ctx['canEdit']) {
        array_push($tabs, ['menu', 'menu', 'Κατάλογος'], ['settings', 'settings', 'Ρυθμίσεις & σύνδεσμος']);
    }
    return module_tabs($tabs, $current);
}
