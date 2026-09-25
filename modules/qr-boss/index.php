<?php
declare(strict_types=1);

// QR Boss μέσα στην πύλη. Διαθέσιμα: $user, $business, $tool, $subscription, $subpath
require __DIR__ . '/lib.php';

$bid = (int) $business['id'];
$pro = qr_is_pro($subscription);
$canEdit = has_role('owner', 'manager');
$profile = qr_profile($bid);

// Κοινά δεδομένα για όλες τις προβολές (οι σελίδες τρέχουν μέσα σε συνάρτηση, όχι global)
$GLOBALS['qr_common'] = compact('business', 'subscription', 'pro', 'profile', 'canEdit', 'bid', 'user');

// [μοτίβο, σελίδα]
$qrRoutes = [
    ['',                        'overview'],
    ['codes',                   'codes'],
    ['codes/(\d+)',             'code'],
    ['print',                   'print'],
    ['menu',                    'menu'],
    ['menu/item/(\d+|new)',     'item'],
    ['menu/category/(\d+)',     'category'],
    ['preview',                 'preview'],
    ['calls',                   'calls'],
    ['calls/feed',              'calls_feed'],
    ['settings',                'settings'],
];

foreach ($qrRoutes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

/** Κοινές επιλογές για τις σελίδες του QR Boss */
function qr_page(string $view, array $data, string $title, string $tab): never
{
    module_render('qr-boss', $view, $data + $GLOBALS['qr_common'] + ['tab' => $tab], ['title' => $title . ' · QR Boss']);
}

function qr_require_edit(): void
{
    if (!has_role('owner', 'manager')) {
        forbidden();
    }
}
