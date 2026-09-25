<?php
declare(strict_types=1);

// Μηχανή κρατήσεων: /p/hotel-booking/<κωδικός>[/b/<token>]
require_once __DIR__ . '/lib.php';

$parts = explode('/', $subpath);
$st = q1('SELECT * FROM hb_settings WHERE code = ?', [$parts[0] ?? '']);
$tool = q1("SELECT id FROM tools WHERE slug = 'hotel-booking'");
if (!$st || !$tool || !active_subscription((int) $st['business_id'], (int) $tool['id'])) {
    not_found();
}
$bid = (int) $st['business_id'];
$base = 'p/hotel-booking/' . $st['code'];
$page = fn(string $view, array $data = []) => public_page(__DIR__ . '/views/' . $view . '.php', $data + ['st' => $st, 'base' => $base], $st['title'], $st['color']);

if (($parts[1] ?? '') === 'b' && !empty($parts[2])) {
    $b = q1('SELECT b.*, r.name AS room FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id WHERE b.token = ? AND b.business_id = ?', [$parts[2], $bid]);
    if (!$b) {
        not_found();
    }
    $page('public_booking', compact('b'));
}

$in = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_REQUEST['in'] ?? '')) ? $_REQUEST['in'] : '';
$out = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_REQUEST['out'] ?? '')) ? $_REQUEST['out'] : '';
$guests = max(1, min(20, (int) ($_REQUEST['guests'] ?? 2)));
$error = null;
$offers = [];
if ($in && $out) {
    $nights = hb_nights($in, $out);
    if ($in < date('Y-m-d') || $nights < 1) {
        $error = 'Διαλέξτε σωστές ημερομηνίες.';
    } elseif ($nights < (int) $st['min_nights']) {
        $error = 'Ελάχιστη διαμονή ' . (int) $st['min_nights'] . ' βράδια.';
    } elseif ($nights > 60) {
        $error = 'Για διαμονές άνω των 60 ημερών επικοινωνήστε μαζί μας.';
    } else {
        foreach (qall('SELECT * FROM hb_rooms WHERE business_id = ? AND active = 1 AND capacity >= ? ORDER BY sort, base_price_cents', [$bid, $guests]) as $r) {
            if (hb_free_units($r, $in, $out) > 0) {
                $offers[] = $r + ['total' => hb_price($r, $in, $out)];
            }
        }
    }
}

if (is_post() && !$error && $in && $out) {
    $room = null;
    foreach ($offers as $o) {
        if ((int) $o['id'] === input_int('room_id')) {
            $room = $o;
        }
    }
    $email = input('email');
    if (!$room) {
        $error = 'Το δωμάτιο δεν είναι πια διαθέσιμο.';
    } elseif (input('name') === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen(preg_replace('/\D/', '', input('phone'))) < 8) {
        $error = 'Συμπληρώστε όνομα, email και τηλέφωνο.';
    } else {
        $lock = 'hb_room_' . $room['id'];
        qval('SELECT GET_LOCK(?, 5)', [$lock]);
        if (hb_free_units($room, $in, $out) < 1) {
            qval('SELECT RELEASE_LOCK(?)', [$lock]);
            $error = 'Το δωμάτιο μόλις κλείστηκε.';
        } else {
            $token = bin2hex(random_bytes(16));
            $status = $st['auto_confirm'] ? 'confirmed' : 'pending';
            q('INSERT INTO hb_bookings (business_id, room_id, token, checkin, checkout, guests, name, email, phone, country, notes, total_cents, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
                $bid, $room['id'], $token, $in, $out, $guests, mb_substr(input('name'), 0, 120), $email, mb_substr(input('phone'), 0, 40),
                mb_substr(input('country'), 0, 60) ?: null, mb_substr(input('notes'), 0, 500) ?: null, $room['total'], $status,
            ]);
            $id = (int) db()->lastInsertId();
            qval('SELECT RELEASE_LOCK(?)', [$lock]);
            hb_mail_guest($id, $status);
            notify($bid, 'Νέα κράτηση: ' . input('name') . ' · ' . $room['name'] . ' · ' . date_gr($in, false) . '–' . date_gr($out, false), 't/hotel-booking/bookings/' . $id);
            header('Location: ' . url($base . '/b/' . $token . '?new=1'));
            exit;
        }
    }
}
$page('public_search', compact('in', 'out', 'guests', 'offers', 'error'));
