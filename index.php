<?php
declare(strict_types=1);

require __DIR__ . '/app/bootstrap.php';

// Η διαδρομή μετά τον φάκελο της εφαρμογής, π.χ. "tools/qr-boss"
$path = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
$path = rawurldecode($path);
if (base_path() !== '' && str_starts_with($path, base_path())) {
    $path = substr($path, strlen(base_path()));
}
$path = trim($path, '/');
if ($path === 'index.php') {
    $path = '';
}

// Αν δεν έχει γίνει εγκατάσταση, στείλε στο install.php
try {
    db()->query('SELECT 1 FROM users LIMIT 1');
} catch (PDOException $e) {
    header('Location: ' . url('install.php'));
    exit;
}

$pages = APP_ROOT . '/app/pages/';

// [μοτίβο, αρχείο σελίδας]. Οι ομάδες του μοτίβου περνούν ως $params.
$routes = [
    ['',                          'home'],
    ['login',                     'login'],
    ['logout',                    'logout'],
    ['forgot',                    'forgot'],
    ['reset/([a-f0-9]{64})',      'reset'],
    ['invite/([a-f0-9]{64})',     'invite'],
    ['dashboard',                 'dashboard'],
    ['tools',                     'tools'],
    ['tools/([a-z0-9-]+)',        'tool'],
    ['team',                      'team'],
    ['billing',                   'billing'],
    ['invoices/(\d+)',            'invoice'],
    ['support',                   'support'],
    ['support/(\d+)',             'ticket'],
    ['notifications',             'notifications'],
    ['profile',                   'profile'],
    ['business',                  'business'],
    ['switch',                    'switch'],
    ['admin',                     'admin/overview'],
    ['admin/requests',            'admin/requests'],
    ['admin/businesses',          'admin/businesses'],
    ['admin/businesses/(\d+)',    'admin/business'],
    ['admin/invoices',            'admin/invoices'],
    ['admin/tickets',             'admin/tickets'],
    ['admin/tickets/(\d+)',       'admin/ticket'],
    ['admin/tools',               'admin/tools'],
    ['admin/tools/(\d+)',         'admin/tool'],
    ['admin/settings',            'admin/settings'],
];

// Κάθε POST (και των εργαλείων) πρέπει να έχει έγκυρο CSRF token
if (is_post()) {
    csrf_check();
}

// Δημόσιες σελίδες εργαλείων (χωρίς login). Το /q/<κωδικός> είναι σύντομη μορφή για το QR Boss.
if (preg_match('#^q/([A-Za-z0-9]{4,16})(/.*)?$#', $path, $m)) {
    dispatch_public('qr-boss', $m[1] . ($m[2] ?? ''));
}
if (preg_match('#^p/([a-z0-9-]+)(?:/(.*))?$#', $path, $m)) {
    dispatch_public($m[1], $m[2] ?? '');
}

// Εργαλεία: /t/<slug>/...
if (preg_match('#^t/([a-z0-9-]+)(?:/(.*))?$#', $path, $m)) {
    dispatch_module($m[1], $m[2] ?? '');
}

foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $path, $m)) {
        $params = array_slice($m, 1);
        require $pages . $page . '.php';
        exit;
    }
}

not_found();
