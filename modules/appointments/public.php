<?php
declare(strict_types=1);

// Δημόσια σελίδα κρατήσεων: /p/appointments/<κωδικός>[/b/<token>]
require_once __DIR__ . '/lib.php';

$parts = explode('/', $subpath);
$st = q1('SELECT * FROM ap_settings WHERE code = ?', [$parts[0] ?? '']);
$tool = q1("SELECT id FROM tools WHERE slug = 'appointments'");
if (!$st || !$tool || !active_subscription((int) $st['business_id'], (int) $tool['id'])) {
    not_found();
}
$bid = (int) $st['business_id'];
$logo = null;
try {
    $logo = qval('SELECT logo FROM qr_profiles WHERE business_id = ?', [$bid]) ?: null;
} catch (PDOException $e) {
}
$base = 'p/appointments/' . $st['code'];
$page = fn(string $view, array $data = []) => public_page(__DIR__ . '/views/' . $view . '.php', $data + ['st' => $st, 'base' => $base], $st['title'], $st['color'], $logo);

// Διαχείριση κράτησης από τον πελάτη
if (($parts[1] ?? '') === 'b' && !empty($parts[2])) {
    $b = q1('SELECT b.*, s.name AS staff FROM ap_bookings b JOIN ap_staff s ON s.id = b.staff_id WHERE b.token = ? AND b.business_id = ?', [$parts[2], $bid]);
    if (!$b) {
        not_found();
    }
    $canCancel = in_array($b['status'], ['pending', 'confirmed'], true) && strtotime($b['starts_at']) - time() >= (int) $st['cancel_hours'] * 3600;
    if (is_post() && input('do') === 'cancel' && $canCancel) {
        q("UPDATE ap_bookings SET status = 'cancelled' WHERE id = ?", [$b['id']]);
        notify($bid, 'Ακύρωση ραντεβού από πελάτη: ' . ap_dt($b['starts_at']), 't/appointments/bookings/' . $b['id']);
        ap_mail_customer((int) $b['id'], 'cancelled');
        header('Location: ' . url($base . '/b/' . $b['token']));
        exit;
    }
    $page('public_booking', compact('b', 'canCancel'));
}

$services = qall('SELECT s.* FROM ap_services s WHERE s.business_id = ? AND s.active = 1
                  AND EXISTS (SELECT 1 FROM ap_staff_services x JOIN ap_staff f ON f.id = x.staff_id WHERE x.service_id = s.id AND f.active = 1)
                  ORDER BY s.sort, s.name', [$bid]);
$service = null;
foreach ($services as $s) {
    if ((int) $s['id'] === (int) ($_REQUEST['service'] ?? 0)) {
        $service = $s;
    }
}
$staffList = $service ? ap_staff_for($bid, (int) $service['id']) : [];
$staffId = (int) ($_REQUEST['staff'] ?? 0); // 0 = όποιος είναι διαθέσιμος
if ($staffId && !in_array($staffId, array_map('intval', array_column($staffList, 'id')), true)) {
    $staffId = 0;
}
$lastDay = date('Y-m-d', strtotime('+' . (int) $st['max_days_ahead'] . ' days'));
$day = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_REQUEST['day'] ?? '')) ? $_REQUEST['day'] : null;
if ($day && ($day < date('Y-m-d') || $day > $lastDay)) {
    $day = null;
}

/** Ελεύθερες ώρες: [ώρα => staff_id] */
$freeFor = function (string $d) use ($st, $staffList, $staffId, $service): array {
    $out = [];
    foreach ($staffList as $f) {
        if ($staffId && (int) $f['id'] !== $staffId) {
            continue;
        }
        foreach (ap_free_slots($st, (int) $f['id'], $d, (int) $service['duration_min']) as $t) {
            $out[$t] ??= (int) $f['id'];
        }
    }
    ksort($out);
    return $out;
};

// Ολοκλήρωση κράτησης
if (is_post() && $service && $day) {
    $time = input('time');
    $name = mb_substr(input('name'), 0, 120);
    $phone = input('phone');
    $email = filter_var(input('email'), FILTER_VALIDATE_EMAIL) ? input('email') : null;
    $free = $freeFor($day);
    if ($name === '' || strlen(preg_replace('/\D/', '', $phone)) < 10 || !isset($free[$time])) {
        $page('public_book', compact('services', 'service', 'staffList', 'staffId', 'day', 'lastDay') + ['free' => $free, 'error' => isset($free[$time]) ? 'Συμπληρώστε όνομα και κινητό.' : 'Η ώρα μόλις κλείστηκε. Διαλέξτε άλλη.']);
    }
    // Προστασία από κατάχρηση: έως 3 μελλοντικά ραντεβού ανά τηλέφωνο
    $cid = ap_customer($bid, $name, $phone, $email);
    if ((int) qval("SELECT COUNT(*) FROM ap_bookings WHERE customer_id = ? AND starts_at > NOW() AND status IN ('pending','confirmed')", [$cid]) >= 3) {
        $page('public_book', compact('services', 'service', 'staffList', 'staffId', 'day', 'lastDay') + ['free' => $free, 'error' => 'Έχετε ήδη 3 ραντεβού. Επικοινωνήστε μαζί μας τηλεφωνικά.']);
    }
    $status = $st['auto_confirm'] ? 'confirmed' : 'pending';
    $id = ap_book($st, $service, $free[$time], $day, $time, $cid, 'online', mb_substr(input('notes'), 0, 500) ?: null, $status);
    if (!$id) {
        $page('public_book', compact('services', 'service', 'staffList', 'staffId', 'day', 'lastDay') + ['free' => $freeFor($day), 'error' => 'Η ώρα μόλις κλείστηκε. Διαλέξτε άλλη.']);
    }
    ap_mail_customer($id, $status);
    notify($bid, 'Νέο ραντεβού: ' . $name . ' · ' . $service['name'] . ' · ' . ap_dt("$day $time"), 't/appointments/bookings/' . $id);
    $token = qval('SELECT token FROM ap_bookings WHERE id = ?', [$id]);
    header('Location: ' . url($base . '/b/' . $token . '?new=1'));
    exit;
}

// Διαθέσιμες ημέρες για τις επόμενες 14 (για γρήγορη επιλογή)
$days = [];
if ($service) {
    for ($i = 0; $i < min(21, (int) $st['max_days_ahead'] + 1); $i++) {
        $d = date('Y-m-d', strtotime("+$i days"));
        $days[$d] = count($freeFor($d));
    }
}
$page('public_book', compact('services', 'service', 'staffList', 'staffId', 'day', 'days', 'lastDay') + ['free' => $service && $day ? $freeFor($day) : []]);
