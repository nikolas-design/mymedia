<?php
if (is_post()) {
    qr_require_edit();
    $type = array_key_exists(input('type'), QR_TYPES) ? input('type') : 'menu';

    if (input('action') === 'tables') {
        // Μαζική δημιουργία: ένα QR μενού ανά τραπέζι
        $from = max(1, input_int('from'));
        $to = min($from + 99, max($from, input_int('to')));
        $prefix = input('prefix') ?: 'Τραπέζι';
        for ($n = $from; $n <= $to; $n++) {
            $label = mb_substr($prefix . ' ' . $n, 0, 40);
            q('INSERT INTO qr_codes (business_id, code, name, type, table_label) VALUES (?, ?, ?, ?, ?)',
                [$bid, qr_new_code(), $label, 'menu', $label]);
        }
        flash('Δημιουργήθηκαν ' . ($to - $from + 1) . ' QR για τραπέζια.');
        redirect('t/qr-boss/codes');
    }

    $name = mb_substr(input('name'), 0, 120);
    $url = null;
    if ($type === 'link') {
        $url = qr_clean_url(input('target_url'));
        if (!$url) {
            flash('Γράψε έγκυρο σύνδεσμο, π.χ. https://instagram.com/…', 'error');
            redirect('t/qr-boss/codes');
        }
    }
    if ($name === '') {
        $name = qr_type_label($type);
    }
    q('INSERT INTO qr_codes (business_id, code, name, type, target_url, table_label) VALUES (?, ?, ?, ?, ?, ?)',
        [$bid, qr_new_code(), $name, $type, $url, mb_substr(input('table_label'), 0, 40) ?: null]);
    $id = (int) db()->lastInsertId();
    flash('Το QR δημιουργήθηκε. Κατέβασέ το ή τύπωσέ το.');
    redirect('t/qr-boss/codes/' . $id);
}

$codes = qall("SELECT c.*, (SELECT COUNT(*) FROM qr_scans s WHERE s.qr_id = c.id AND s.scanned_at >= CURDATE() - INTERVAL 29 DAY) AS n
               FROM qr_codes c WHERE c.business_id = ? ORDER BY c.active DESC, c.table_label IS NULL, LENGTH(c.table_label), c.table_label, c.id", [$bid]);
qr_page('codes', compact('codes'), 'Κωδικοί QR', 'codes');
