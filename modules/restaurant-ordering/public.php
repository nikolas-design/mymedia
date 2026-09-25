<?php
declare(strict_types=1);

// Δημόσια σελίδα παραγγελιών: /p/restaurant-ordering/<κωδικός>[/o/<token>]
require_once __DIR__ . '/lib.php';

$parts = explode('/', $subpath);
$st = q1('SELECT * FROM ro_settings WHERE code = ?', [$parts[0] ?? '']);
$tool = q1("SELECT id FROM tools WHERE slug = 'restaurant-ordering'");
if (!$st || !$tool || !active_subscription((int) $st['business_id'], (int) $tool['id'])) {
    not_found();
}
$bid = (int) $st['business_id'];
$base = 'p/restaurant-ordering/' . $st['code'];
$logo = null;
try {
    $logo = qval('SELECT logo FROM qr_profiles WHERE business_id = ?', [$bid]) ?: null;
} catch (PDOException $e) {
}
$page = fn(string $view, array $data = []) => public_page(__DIR__ . '/views/' . $view . '.php', $data + ['st' => $st, 'base' => $base], $st['title'], $st['color'], $logo);

// Κατάσταση παραγγελίας (με αυτόματη ανανέωση)
if (($parts[1] ?? '') === 'o' && !empty($parts[2])) {
    $o = q1('SELECT * FROM ro_orders WHERE token = ? AND business_id = ?', [$parts[2], $bid]);
    if (!$o) {
        not_found();
    }
    if (isset($_GET['json'])) {
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['status' => $o['status'], 'text' => ro_customer_status($o)]);
        exit;
    }
    $lines = qall('SELECT * FROM ro_order_lines WHERE order_id = ?', [$o['id']]);
    $page('public_status', compact('o', 'lines'));
}

$categories = qall('SELECT * FROM ro_categories WHERE business_id = ? ORDER BY sort, id', [$bid]);
$items = [];
$byId = [];
foreach (qall('SELECT * FROM ro_items WHERE business_id = ? ORDER BY sort, id', [$bid]) as $i) {
    $items[(int) $i['category_id']][] = $i;
    $byId[(int) $i['id']] = $i;
}

if (is_post()) {
    $error = null;
    $cart = json_decode((string) ($_POST['cart'] ?? '[]'), true);
    $lines = [];
    $subtotal = 0;
    foreach (is_array($cart) ? array_slice($cart, 0, 60) : [] as $row) {
        $item = $byId[(int) ($row['id'] ?? 0)] ?? null;
        $qty = max(1, min(50, (int) ($row['qty'] ?? 1)));
        if ($item && $item['available']) {
            $lines[] = [$item, $qty, mb_substr((string) ($row['note'] ?? ''), 0, 190)];
            $subtotal += $qty * (int) $item['price_cents'];
        }
    }
    $kind = input('kind') === 'takeaway' ? 'takeaway' : 'delivery';
    if (($kind === 'delivery' && !$st['delivery']) || ($kind === 'takeaway' && !$st['takeaway'])) {
        $kind = $st['delivery'] ? 'delivery' : 'takeaway';
    }
    $name = mb_substr(input('name'), 0, 120);
    $phone = preg_replace('/[^0-9+]/', '', input('phone'));
    if (!$st['accepting']) {
        $error = 'Αυτή τη στιγμή δεν δεχόμαστε online παραγγελίες.';
    } elseif (!$lines) {
        $error = 'Το καλάθι είναι άδειο.';
    } elseif ($subtotal < (int) $st['min_order_cents']) {
        $error = 'Η ελάχιστη παραγγελία είναι ' . money($st['min_order_cents']) . '.';
    } elseif ($name === '' || strlen($phone) < 10) {
        $error = 'Συμπληρώστε όνομα και κινητό.';
    } elseif ($kind === 'delivery' && mb_strlen(input('address')) < 5) {
        $error = 'Συμπληρώστε διεύθυνση.';
    } elseif ((int) qval("SELECT COUNT(*) FROM ro_orders WHERE business_id = ? AND phone = ? AND created_at > NOW() - INTERVAL 10 MINUTE", [$bid, $phone]) >= 3) {
        $error = 'Πολλές παραγγελίες σε λίγο χρόνο. Καλέστε μας τηλεφωνικά.';
    }
    if ($error) {
        $page('public_menu', compact('categories', 'items', 'error'));
    }
    $fee = $kind === 'delivery' ? (int) $st['delivery_fee_cents'] : 0;
    $token = bin2hex(random_bytes(16));
    db()->beginTransaction();
    $number = (int) qval('SELECT COALESCE(MAX(number), 0) + 1 FROM ro_orders WHERE business_id = ? AND created_at >= CURDATE() FOR UPDATE', [$bid]);
    q('INSERT INTO ro_orders (business_id, number, token, kind, name, phone, address, floor_bell, notes, payment, subtotal_cents, fee_cents, total_cents) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
        $bid, $number, $token, $kind, $name, $phone, $kind === 'delivery' ? mb_substr(input('address'), 0, 255) : null,
        $kind === 'delivery' ? (mb_substr(input('floor_bell'), 0, 120) ?: null) : null, mb_substr(input('notes'), 0, 500) ?: null,
        input('payment') === 'card' ? 'card' : 'cash', $subtotal, $fee, $subtotal + $fee,
    ]);
    $oid = (int) db()->lastInsertId();
    foreach ($lines as [$item, $qty, $note]) {
        q('INSERT INTO ro_order_lines (order_id, item_id, name, qty, price_cents, note) VALUES (?, ?, ?, ?, ?, ?)', [$oid, $item['id'], $item['name'], $qty, $item['price_cents'], $note ?: null]);
    }
    db()->commit();
    notify($bid, 'Νέα παραγγελία #' . $number . ' · ' . money($subtotal + $fee), 't/restaurant-ordering');
    header('Location: ' . url($base . '/o/' . $token));
    exit;
}
$page('public_menu', compact('categories', 'items'));
