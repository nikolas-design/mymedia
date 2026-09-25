<?php
declare(strict_types=1);

// Websites μέσα στην πύλη (πλευρά πελάτη)
require __DIR__ . '/lib.php';

$site = ws_site($bid);
if ($subpath === '' ) {
    if (is_post()) {
        $title = mb_substr(input('title'), 0, 160);
        if ($title === '' || input('description') === '') {
            flash('Γράψε τίτλο και περιγραφή.', 'error');
            redirect('t/websites');
        }
        try {
            $photo = store_image('photo', 'websites/' . $bid, 1800);
        } catch (RuntimeException $e) {
            flash($e->getMessage(), 'error');
            redirect('t/websites');
        }
        q('INSERT INTO ws_requests (business_id, user_id, title, description, page_url, urgent, photo) VALUES (?, ?, ?, ?, ?, ?, ?)', [
            $bid, $user['id'], $title, mb_substr(input('description'), 0, 5000), mb_substr(input('page_url'), 0, 255) ?: null, input('urgent') === '1' ? 1 : 0, $photo,
        ]);
        notify(null, 'Αίτημα site: ' . $business['name'] . ' · ' . $title, 'admin/t/websites/' . db()->lastInsertId());
        flash('Το αίτημα στάλθηκε. Θα σε ενημερώσουμε μόλις γίνει.');
        redirect('t/websites');
    }
    $requests = qall('SELECT r.*, u.name AS by_name FROM ws_requests r LEFT JOIN users u ON u.id = r.user_id WHERE r.business_id = ? ORDER BY r.id DESC LIMIT 50', [$bid]);
    $used = ws_month_used($bid);
    module_page('overview', compact('site', 'requests', 'used'), 'Το site μου', 'overview');
}
if (preg_match('#^requests/(\d+)$#', $subpath, $m)) {
    $r = q1('SELECT * FROM ws_requests WHERE id = ? AND business_id = ?', [(int) $m[1], $bid]);
    if (!$r) {
        not_found();
    }
    module_page('request', compact('r'), $r['title'], 'overview');
}
not_found();
