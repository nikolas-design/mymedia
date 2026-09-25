<?php
declare(strict_types=1);

// Hotel Booking μέσα στην πύλη
require __DIR__ . '/lib.php';

$settings = hb_settings($bid);
$routes = [
    ['',                 'calendar'],
    ['bookings',         'bookings'],
    ['bookings/(\d+|new)', 'booking'],
    ['rooms',            'rooms'],
    ['rooms/(\d+|new)',  'room'],
    ['settings',         'settings'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function hb_tabs(string $current): string
{
    $ctx = $GLOBALS['module_ctx'];
    $pending = (int) qval("SELECT COUNT(*) FROM hb_bookings WHERE business_id = ? AND status = 'pending'", [$ctx['bid']]);
    $tabs = [['calendar', '', 'Διαθεσιμότητα'], ['bookings', 'bookings', 'Κρατήσεις', $pending]];
    if ($ctx['canEdit']) {
        array_push($tabs, ['rooms', 'rooms', 'Δωμάτια & τιμές'], ['settings', 'settings', 'Μηχανή κρατήσεων']);
    }
    return module_tabs($tabs, $current);
}
