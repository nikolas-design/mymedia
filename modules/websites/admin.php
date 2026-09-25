<?php
declare(strict_types=1);

// Websites: διαχείριση από την ομάδα MyMedia (/admin/t/websites)
require __DIR__ . '/lib.php';

$page = fn(string $view, array $data, string $title) => render('../../modules/websites/views/' . $view, $data, ['title' => $title, 'nav' => 'admin-tools', 'area' => 'admin']);

if (preg_match('#^(\d+)$#', $subpath, $m)) {
    $r = q1('SELECT r.*, b.name AS business_name, u.name AS by_name, u.email FROM ws_requests r JOIN businesses b ON b.id = r.business_id
             LEFT JOIN users u ON u.id = r.user_id WHERE r.id = ?', [(int) $m[1]]);
    if (!$r) {
        not_found();
    }
    if (is_post()) {
        $status = array_key_exists(input('status'), WS_STATUS) ? input('status') : $r['status'];
        q('UPDATE ws_requests SET status = ?, reply = ?, updated_at = NOW() WHERE id = ?', [$status, input('reply') ?: null, $r['id']]);
        if ($status !== $r['status'] || input('reply') !== (string) $r['reply']) {
            notify((int) $r['business_id'], 'Αίτημα site «' . $r['title'] . '»: ' . WS_STATUS[$status][0], 't/websites/requests/' . $r['id']);
            if ($r['email'] && in_array($status, ['done', 'rejected'], true)) {
                send_mail($r['email'], 'Το αίτημά σας: ' . $r['title'], "Γεια σας {$r['by_name']},\n\nΤο αίτημα «{$r['title']}»: " . WS_STATUS[$status][0] . ".\n\n"
                    . (input('reply') ? input('reply') . "\n\n" : '') . full_url('t/websites/requests/' . $r['id']));
            }
        }
        flash('Αποθηκεύτηκε.');
        redirect('admin/t/websites');
    }
    $page('admin_request', compact('r'), $r['title']);
}
if (preg_match('#^site/(\d+)$#', $subpath, $m)) {
    $b = q1('SELECT * FROM businesses WHERE id = ?', [(int) $m[1]]);
    if (!$b) {
        not_found();
    }
    $site = ws_site((int) $b['id']);
    if (is_post()) {
        $d = fn(string $k) => preg_match('/^\d{4}-\d{2}-\d{2}$/', input($k)) ? input($k) : null;
        q('UPDATE ws_sites SET domain = ?, site_url = ?, platform = ?, domain_until = ?, hosting_until = ?, ssl_until = ?, changes_per_month = ?, notes = ? WHERE business_id = ?', [
            input('domain') ?: null, input('site_url') ?: null, input('platform') ?: null, $d('domain_until'), $d('hosting_until'), $d('ssl_until'),
            max(0, min(99, input_int('changes_per_month'))), input('notes') ?: null, $b['id'],
        ]);
        flash('Αποθηκεύτηκε.');
        redirect('admin/t/websites');
    }
    $page('admin_site', compact('b', 'site'), 'Site · ' . $b['name']);
}
if ($subpath !== '') {
    not_found();
}
$open = qall("SELECT r.*, b.name AS business_name FROM ws_requests r JOIN businesses b ON b.id = r.business_id
              WHERE r.status IN ('new','in_progress') ORDER BY r.urgent DESC, r.id", []);
$sites = qall("SELECT b.id, b.name, w.domain, w.domain_until, w.hosting_until, w.ssl_until FROM subscriptions s
               JOIN businesses b ON b.id = s.business_id LEFT JOIN ws_sites w ON w.business_id = b.id
               WHERE s.status = 'active' AND s.tool_id = ? ORDER BY b.name", [$tool['id']]);
$page('admin_overview', compact('open', 'sites'), 'Websites');
