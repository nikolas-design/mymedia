<?php
declare(strict_types=1);

// Appointments μέσα στην πύλη
require __DIR__ . '/lib.php';

$settings = ap_settings($bid);
$routes = [
    ['',                   'calendar'],
    ['new',                'new'],
    ['slots',              'slots'],
    ['bookings/(\d+)',     'booking'],
    ['services',           'services'],
    ['staff',              'staff'],
    ['staff/(\d+|new)',    'staff_edit'],
    ['customers',          'customers'],
    ['customers/(\d+)',    'customer'],
    ['settings',           'settings'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function ap_tabs(string $current): string
{
    $ctx = $GLOBALS['module_ctx'];
    $pending = (int) qval("SELECT COUNT(*) FROM ap_bookings WHERE business_id = ? AND status = 'pending' AND starts_at >= NOW()", [$ctx['bid']]);
    $tabs = [['calendar', '', 'Ημερολόγιο', $pending], ['new', 'new', 'Νέο ραντεβού'], ['customers', 'customers', 'Πελάτες']];
    if ($ctx['canEdit']) {
        array_push($tabs, ['services', 'services', 'Υπηρεσίες'], ['staff', 'staff', 'Συνεργάτες'], ['settings', 'settings', 'Σελίδα κρατήσεων']);
    }
    return module_tabs($tabs, $current);
}
