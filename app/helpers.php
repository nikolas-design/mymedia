<?php
declare(strict_types=1);

function e(mixed $v): string
{
    return htmlspecialchars((string) $v, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Ο φάκελος όπου τρέχει η εφαρμογή, π.χ. "/mymedia" (ή "" στη ρίζα) */
function base_path(): string
{
    static $base = null;
    if ($base === null) {
        $base = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/')), '/');
    }
    return $base;
}

function url(string $path = ''): string
{
    return base_path() . '/' . ltrim($path, '/');
}

function full_url(string $path = ''): string
{
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    return ($https ? 'https' : 'http') . '://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . url($path);
}

function asset(string $path): string
{
    $file = APP_ROOT . '/assets/' . $path;
    $v = is_file($file) ? filemtime($file) : 0;
    return url('assets/' . $path) . '?v=' . $v;
}

function redirect(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function is_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function input(string $key, string $default = ''): string
{
    $v = $_POST[$key] ?? $default;
    return is_string($v) ? trim($v) : $default;
}

function input_int(string $key): int
{
    return (int) ($_POST[$key] ?? 0);
}

/* ---------- Μορφοποίηση ---------- */

/** 4960 → "€49,60" */
function money(int|string|null $cents): string
{
    return '€' . number_format(((int) $cents) / 100, 2, ',', '.');
}

/** "49,60" ή "49.60" → 4960 */
function parse_money(string $s): int
{
    $s = str_replace(['€', ' '], '', $s);
    if (str_contains($s, ',')) {
        $s = str_replace('.', '', $s);
        $s = str_replace(',', '.', $s);
    }
    return (int) round(((float) $s) * 100);
}

function date_gr(?string $d, bool $withYear = true): string
{
    if (!$d) {
        return '—';
    }
    $t = strtotime($d);
    return date($withYear ? 'd/m/Y' : 'd/m', $t);
}

function ago(?string $d): string
{
    if (!$d) {
        return 'ποτέ';
    }
    $s = time() - strtotime($d);
    if ($s < 60) {
        return 'μόλις τώρα';
    }
    if ($s < 3600) {
        $m = intdiv($s, 60);
        return $m . ($m === 1 ? ' λεπτό' : ' λεπτά') . ' πριν';
    }
    if ($s < 86400) {
        $h = intdiv($s, 3600);
        return $h . ($h === 1 ? ' ώρα' : ' ώρες') . ' πριν';
    }
    $days = intdiv($s, 86400);
    if ($days === 1) {
        return 'χθες';
    }
    if ($days < 30) {
        return $days . ' ημέρες πριν';
    }
    return date_gr($d);
}

function initials(string $name): string
{
    $parts = preg_split('/\s+/u', trim($name)) ?: [];
    $out = '';
    foreach (array_slice($parts, 0, 2) as $p) {
        $out .= mb_strtoupper(mb_substr($p, 0, 1));
    }
    return $out ?: '?';
}

/** Χρώμα avatar σταθερό ανά όνομα */
function avatar_style(string $name): string
{
    $palette = [
        ['#f2edfd', '#793de7'], ['#e6f3fb', '#2f8fd6'], ['#fdf3e2', '#e79b23'],
        ['#fbe9ef', '#d64b7c'], ['#e3f5f2', '#1d9e75'], ['#e9ecfb', '#4c5fd8'],
    ];
    [$bg, $fg] = $palette[crc32($name) % count($palette)];
    return "background:$bg;color:$fg";
}

/** Ετικέτα κατάστασης */
function pill(string $text, string $tone = 'grey'): string
{
    return '<span class="pill pill-' . e($tone) . '">' . e($text) . '</span>';
}

/* ---------- Χρήματα συνδρομών ---------- */

const VAT_RATE = 24;

/** Μηνιαίο ισοδύναμο μιας συνδρομής σε λεπτά */
function monthly_cents(array $sub): int
{
    $p = (int) $sub['price_cents'];
    return $sub['billing'] === 'year' ? (int) round($p / 12) : $p;
}

function period_label(string $billing): string
{
    return $billing === 'year' ? '/έτος' : '/μήνα';
}

/* ---------- CSRF & μηνύματα ---------- */

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_check(): void
{
    $t = $_POST['_csrf'] ?? '';
    if (!is_string($t) || !hash_equals(csrf_token(), $t)) {
        http_response_code(419);
        flash('Η φόρμα έληξε. Δοκίμασε ξανά.', 'error');
        header('Location: ' . ($_SERVER['REQUEST_URI'] ?? url()));
        exit;
    }
}

function flash(string $msg, string $type = 'ok'): void
{
    $_SESSION['flash'][] = ['msg' => $msg, 'type' => $type];
}

function take_flashes(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------- Προβολή ---------- */

/**
 * Εμφανίζει μια σελίδα μέσα στο layout.
 * $opts: title, nav (ενεργό στοιχείο μενού), area ('client'|'admin'|'auth')
 */
function render(string $view, array $data = [], array $opts = []): never
{
    extract($data, EXTR_SKIP);
    ob_start();
    require APP_ROOT . '/app/views/' . $view . '.php';
    $content = ob_get_clean();

    $title = $opts['title'] ?? '';
    $nav   = $opts['nav'] ?? '';
    $area  = $opts['area'] ?? 'client';
    require APP_ROOT . '/app/views/layout.php';
    exit;
}

function not_found(): never
{
    http_response_code(404);
    render('message', [
        'heading' => 'Δεν βρέθηκε',
        'text'    => 'Η σελίδα που ζήτησες δεν υπάρχει ή δεν έχεις πρόσβαση σε αυτή.',
    ], ['title' => 'Δεν βρέθηκε', 'area' => current_user() ? 'client' : 'auth']);
}

function forbidden(): never
{
    http_response_code(403);
    render('message', [
        'heading' => 'Χωρίς πρόσβαση',
        'text'    => 'Ο ρόλος σου δεν επιτρέπει αυτή την ενέργεια. Μίλα με τον ιδιοκτήτη του λογαριασμού.',
    ], ['title' => 'Χωρίς πρόσβαση']);
}

/* ---------- Email ---------- */

function send_mail(string $to, string $subject, string $body): bool
{
    $from = $GLOBALS['config']['mail_from'] ?? 'no-reply@' . ($_SERVER['HTTP_HOST'] ?? 'localhost');
    $name = $GLOBALS['config']['app_name'] ?? 'MyMedia';
    $headers = [
        'From: =?UTF-8?B?' . base64_encode($name) . "?= <$from>",
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
    ];
    return @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));
}
