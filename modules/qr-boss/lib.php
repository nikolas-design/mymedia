<?php
declare(strict_types=1);

// Κοινά για τις σελίδες του QR Boss (πύλη και δημόσιες)

const QR_TYPES = [
    'menu'   => ['Ψηφιακό μενού', 'utensils'],
    'link'   => ['Σύνδεσμος', 'arrow'],
    'wifi'   => ['Wi-Fi', 'globe'],
    'review' => ['Κριτική Google', 'star'],
];

const QR_TAGS = [
    'new'   => 'Νέο',
    'top'   => 'Δημοφιλές',
    'veg'   => 'Vegetarian',
    'vegan' => 'Vegan',
    'gf'    => 'Χωρίς γλουτένη',
    'spicy' => 'Πικάντικο',
];

function qr_is_pro(array $subscription): bool
{
    return stripos((string) $subscription['plan_name'], 'pro') !== false;
}

/** Το προφίλ μενού της επιχείρησης· δημιουργείται την πρώτη φορά */
function qr_profile(int $businessId): array
{
    $p = q1('SELECT * FROM qr_profiles WHERE business_id = ?', [$businessId]);
    if (!$p) {
        $b = q1('SELECT name, phone, address FROM businesses WHERE id = ?', [$businessId]);
        q('INSERT INTO qr_profiles (business_id, title, phone, address) VALUES (?, ?, ?, ?)',
            [$businessId, $b['name'], $b['phone'], $b['address']]);
        $p = q1('SELECT * FROM qr_profiles WHERE business_id = ?', [$businessId]);
    }
    return $p;
}

/** Νέος σύντομος κωδικός, π.χ. "k7Qm2xA" (χωρίς χαρακτήρες που μπερδεύονται) */
function qr_new_code(): string
{
    $alphabet = 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKLMNPQRSTUVWXYZ23456789';
    do {
        $code = '';
        for ($i = 0; $i < 7; $i++) {
            $code .= $alphabet[random_int(0, strlen($alphabet) - 1)];
        }
    } while (qval('SELECT 1 FROM qr_codes WHERE code = ?', [$code]));
    return $code;
}

function qr_public_url(string $code): string
{
    return full_url('q/' . $code);
}

/** Έγκυρο http(s) URL ή null */
function qr_clean_url(string $url): ?string
{
    $url = trim($url);
    if ($url === '') {
        return null;
    }
    if (!preg_match('#^https?://#i', $url)) {
        $url = 'https://' . $url;
    }
    return filter_var($url, FILTER_VALIDATE_URL) && preg_match('#^https?://#i', $url) ? $url : null;
}

function qr_valid_color(string $c, string $default = '#793de7'): string
{
    return preg_match('/^#[0-9a-fA-F]{6}$/', $c) ? strtolower($c) : $default;
}

/** Σαρώσεις ανά ημέρα για τις τελευταίες $days ημέρες: ['Y-m-d' => n] */
function qr_daily_scans(int $businessId, int $days, ?int $qrId = null): array
{
    $out = [];
    for ($i = $days - 1; $i >= 0; $i--) {
        $out[date('Y-m-d', strtotime("-$i days"))] = 0;
    }
    $sql = 'SELECT DATE(scanned_at) AS d, COUNT(*) AS n FROM qr_scans WHERE business_id = ? AND scanned_at >= ?';
    $args = [$businessId, array_key_first($out) . ' 00:00:00'];
    if ($qrId) {
        $sql .= ' AND qr_id = ?';
        $args[] = $qrId;
    }
    foreach (qall($sql . ' GROUP BY DATE(scanned_at)', $args) as $r) {
        $out[$r['d']] = (int) $r['n'];
    }
    return $out;
}

/** Μπάρες ημερών (όπως στην πύλη) */
function qr_bars(array $daily): string
{
    $max = max(1, max($daily));
    $days = ['Κυ', 'Δε', 'Τρ', 'Τε', 'Πε', 'Πα', 'Σα'];
    $bars = '';
    $labels = '';
    foreach ($daily as $d => $n) {
        $bars .= '<i style="height:' . round($n / $max * 100) . '%" title="' . e(date_gr($d, false)) . ': ' . $n . '"></i>';
        $labels .= '<span>' . $days[(int) date('w', strtotime($d))] . '</span>';
    }
    return '<div class="bars">' . $bars . '</div><div class="barlbl">' . $labels . '</div>';
}

function qr_type_label(string $type): string
{
    return QR_TYPES[$type][0] ?? $type;
}

/** Κατηγορίες και πιάτα για το δημόσιο μενού */
function qr_menu_data(int $businessId): array
{
    $categories = qall('SELECT * FROM qr_categories WHERE business_id = ? AND active = 1 ORDER BY sort, id', [$businessId]);
    $items = [];
    foreach (qall('SELECT i.* FROM qr_items i JOIN qr_categories c ON c.id = i.category_id
                   WHERE i.business_id = ? AND c.active = 1 ORDER BY i.sort, i.id', [$businessId]) as $i) {
        $items[(int) $i['category_id']][] = $i;
    }
    return ['categories' => array_values(array_filter($categories, fn($c) => !empty($items[(int) $c['id']]))), 'items' => $items];
}

/** Δημόσια σελίδα χωρίς το layout της πύλης */
function qr_public_page(string $view, array $data): never
{
    extract($data, EXTR_SKIP);
    $color = qr_valid_color($data['profile']['color'] ?? '#793de7');
    require __DIR__ . '/views/public_layout.php';
    exit;
}
