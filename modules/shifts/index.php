<?php
declare(strict_types=1);

// Βάρδιες μέσα στην πύλη
require __DIR__ . '/lib.php';

$routes = [
    ['',             'week'],
    ['mine',         'mine'],
    ['requests',     'requests'],
    ['people',       'people'],
    ['print',        'print'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function sh_tabs(string $current): string
{
    $ctx = $GLOBALS['module_ctx'];
    $new = (int) qval("SELECT COUNT(*) FROM sh_requests WHERE business_id = ? AND status = 'new'", [$ctx['bid']]);
    $tabs = [['week', '', 'Πρόγραμμα'], ['mine', 'mine', 'Οι βάρδιες μου']];
    $tabs[] = ['requests', 'requests', $ctx['canEdit'] ? 'Αιτήματα' : 'Άδεια / ρεπό', $ctx['canEdit'] ? $new : 0];
    if ($ctx['canEdit']) {
        $tabs[] = ['people', 'people', 'Προσωπικό'];
    }
    return module_tabs($tabs, $current);
}

/** Το άτομο του προγράμματος που αντιστοιχεί στον συνδεδεμένο χρήστη */
function sh_me(int $businessId, array $user): ?array
{
    return q1('SELECT * FROM sh_people WHERE business_id = ? AND (user_id = ? OR (email IS NOT NULL AND email = ?)) AND active = 1 LIMIT 1',
        [$businessId, $user['id'], $user['email']]);
}
