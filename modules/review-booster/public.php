<?php
declare(strict_types=1);

// Δημόσια σελίδα αξιολόγησης: /r/<κωδικός>
require_once __DIR__ . '/lib.php';

[$code, $action] = array_pad(explode('/', $subpath, 2), 2, '');
$loc = q1('SELECT * FROM rb_locations WHERE code = ? AND active = 1', [$code]);
$tool = q1("SELECT id FROM tools WHERE slug = 'review-booster'");
if (!$loc || !$tool || !active_subscription((int) $loc['business_id'], (int) $tool['id'])) {
    http_response_code(404);
    public_page(__DIR__ . '/views/public_gone.php', [], 'Μη διαθέσιμο');
}
$bizName = (string) qval('SELECT name FROM businesses WHERE id = ?', [$loc['business_id']]);
// Το λογότυπο του QR Boss, αν υπάρχει
$logo = null;
try {
    $logo = qval('SELECT logo FROM qr_profiles WHERE business_id = ?', [$loc['business_id']]) ?: null;
} catch (PDOException $e) {
    $logo = null;
}
$page = fn(string $view, array $data = []) => public_page(__DIR__ . '/views/' . $view . '.php', $data + ['loc' => $loc, 'bizName' => $bizName],
    $bizName, $loc['color'], $logo);
$sessKey = 'rb_rating_' . $loc['id'];

// 1. Αστέρια
if ($action === '' && is_post()) {
    $stars = input_int('stars');
    if ($stars < 1 || $stars > 5) {
        redirect('r/' . $code);
    }
    // Μία αξιολόγηση ανά 10 λεπτά από το ίδιο κινητό
    $prev = $_SESSION[$sessKey] ?? null;
    if ($prev && time() - $prev['t'] < 600) {
        $ratingId = $prev['id'];
        q('UPDATE rb_ratings SET stars = ? WHERE id = ?', [$stars, $ratingId]);
    } else {
        $source = in_array($_POST['source'] ?? '', ['email', 'link'], true) ? $_POST['source'] : 'qr';
        q('INSERT INTO rb_ratings (business_id, location_id, stars, source) VALUES (?, ?, ?, ?)', [$loc['business_id'], $loc['id'], $stars, $source]);
        $ratingId = (int) db()->lastInsertId();
    }
    $_SESSION[$sessKey] = ['id' => $ratingId, 't' => time(), 'stars' => $stars];

    if ($stars >= (int) $loc['threshold'] && $loc['google_url']) {
        $page('public_happy', ['stars' => $stars]);
    }
    if ($stars >= (int) $loc['threshold']) {
        $page('public_thanks', []);
    }
    $page('public_feedback', ['stars' => $stars]);
}

// 2. Μετάβαση στο Google
if ($action === 'go') {
    $prev = $_SESSION[$sessKey] ?? null;
    if ($prev) {
        q('UPDATE rb_ratings SET went_google = 1 WHERE id = ?', [$prev['id']]);
    }
    if ($loc['google_url']) {
        header('Location: ' . $loc['google_url'], true, 302);
        exit;
    }
    redirect('r/' . $code);
}

// 3. Ιδιωτικό σχόλιο
if ($action === 'feedback' && is_post()) {
    $prev = $_SESSION[$sessKey] ?? null;
    $msg = trim(mb_substr(input('message'), 0, 3000));
    if (!$prev || $msg === '') {
        redirect('r/' . $code);
    }
    if (empty($prev['fb'])) {
        q('INSERT INTO rb_feedback (business_id, location_id, rating_id, stars, message, name, contact) VALUES (?, ?, ?, ?, ?, ?, ?)', [
            $loc['business_id'], $loc['id'], $prev['id'], $prev['stars'], $msg,
            mb_substr(input('name'), 0, 120) ?: null, mb_substr(input('contact'), 0, 190) ?: null,
        ]);
        notify((int) $loc['business_id'], 'Νέο ιδιωτικό σχόλιο ' . $prev['stars'] . '★ (' . $loc['name'] . ')', 't/review-booster/feedback/' . db()->lastInsertId());
        $_SESSION[$sessKey]['fb'] = true;
    }
    $page('public_thanks', ['private' => true]);
}

if ($action !== '') {
    not_found();
}
$page('public_rate', ['source' => ($_GET['s'] ?? '') === 'email' ? 'email' : 'qr']);
