<?php
declare(strict_types=1);

// Δημόσιες σελίδες QR Boss: /q/<κωδικός> και /q/<κωδικός>/call
require_once __DIR__ . '/lib.php';

[$code, $action] = array_pad(explode('/', $subpath, 2), 2, '');
$qr = q1('SELECT * FROM qr_codes WHERE code = ? AND active = 1', [$code]);
$tool = q1("SELECT * FROM tools WHERE slug = 'qr-boss'");
$subscription = $qr && $tool ? active_subscription((int) $qr['business_id'], (int) $tool['id']) : null;
if (!$qr || !$subscription) {
    http_response_code(404);
    qr_public_page('public_unavailable', []);
}
$bid = (int) $qr['business_id'];
$profile = qr_profile($bid);
$pro = qr_is_pro($subscription);
$canCall = $pro && $profile['waiter_calls'] && $qr['type'] === 'menu' && $qr['table_label'];

// Κλήση σερβιτόρου / λογαριασμού
if ($action === 'call') {
    if (!is_post() || !$canCall) {
        redirect('q/' . $code);
    }
    $kind = input('kind') === 'bill' ? 'bill' : 'waiter';
    $key = 'qr_call_' . $qr['id'] . '_' . $kind;
    // Όχι πάνω από μία κλήση ανά λεπτό από το ίδιο κινητό
    if (time() - (int) ($_SESSION[$key] ?? 0) >= 60) {
        $_SESSION[$key] = time();
        $recent = qval("SELECT 1 FROM qr_calls WHERE qr_id = ? AND kind = ? AND status = 'new'", [$qr['id'], $kind]);
        if (!$recent) {
            q('INSERT INTO qr_calls (business_id, qr_id, table_label, kind) VALUES (?, ?, ?, ?)', [$bid, $qr['id'], $qr['table_label'], $kind]);
        }
    }
    header('Location: ' . url('q/' . $code . '?called=' . $kind));
    exit;
}
if ($action !== '') {
    not_found();
}

// Καταγραφή σαρώσεων (μία ανά 30 λεπτά ανά κινητό, για να μη μετράνε τα refresh)
$seenKey = 'qr_seen_' . $qr['id'];
if (!isset($_GET['called']) && time() - (int) ($_SESSION[$seenKey] ?? 0) > 1800) {
    $_SESSION[$seenKey] = time();
    $mobile = (bool) preg_match('/Mobi|Android|iPhone|iPad/i', $_SERVER['HTTP_USER_AGENT'] ?? '');
    q('INSERT INTO qr_scans (business_id, qr_id, device) VALUES (?, ?, ?)', [$bid, $qr['id'], $mobile ? 'mobile' : 'desktop']);
}

switch ($qr['type']) {
    case 'link':
        if ($qr['target_url']) {
            header('Location: ' . $qr['target_url'], true, 302);
            exit;
        }
        break;
    case 'review':
        if ($profile['google_review_url']) {
            header('Location: ' . $profile['google_review_url'], true, 302);
            exit;
        }
        break;
    case 'wifi':
        qr_public_page('public_wifi', compact('profile', 'qr'));
}

qr_public_page('public_menu', qr_menu_data($bid) + compact('profile', 'qr', 'canCall'));
