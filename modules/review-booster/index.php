<?php
declare(strict_types=1);

// Review Booster μέσα στην πύλη
require __DIR__ . '/lib.php';

$bid = (int) $business['id'];
$routes = [
    ['',                   'overview'],
    ['feedback',           'feedback'],
    ['feedback/(\d+)',     'feedback_item'],
    ['locations',          'locations'],
    ['locations/(\d+)',    'location'],
    ['send',               'send'],
    ['report',             'report'],
    ['templates',          'templates'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function rb_tabs(string $current): string
{
    $ctx = $GLOBALS['module_ctx'];
    $new = (int) qval("SELECT COUNT(*) FROM rb_feedback WHERE business_id = ? AND status = 'new'", [$ctx['bid']]);
    $tabs = [
        ['overview', '', 'Επισκόπηση'],
        ['feedback', 'feedback', 'Ιδιωτικά σχόλια', $new],
        ['locations', 'locations', 'Σημεία & QR'],
        ['send', 'send', 'Αποστολή αιτήματος'],
        ['report', 'report', 'Αναφορά'],
    ];
    if ($ctx['pro']) {
        $tabs[] = ['templates', 'templates', 'Πρότυπα απαντήσεων'];
    }
    return module_tabs($tabs, $current);
}

/** Το πρώτο σημείο δημιουργείται αυτόματα, για να δουλεύει αμέσως */
function rb_ensure_location(int $businessId, string $name): void
{
    if (!qval('SELECT 1 FROM rb_locations WHERE business_id = ?', [$businessId])) {
        do {
            $code = short_code();
        } while (qval('SELECT 1 FROM rb_locations WHERE code = ?', [$code]));
        q('INSERT INTO rb_locations (business_id, name, code) VALUES (?, ?, ?)', [$businessId, $name, $code]);
    }
}
