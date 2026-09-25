<?php
$c = q1('SELECT * FROM qr_codes WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$c) {
    not_found();
}
$here = 't/qr-boss/codes/' . $c['id'];

if (is_post()) {
    qr_require_edit();
    if (input('action') === 'delete') {
        q('DELETE FROM qr_codes WHERE id = ?', [$c['id']]);
        flash('Το QR διαγράφηκε. Αν είναι τυπωμένο κάπου, δεν θα ανοίγει πια.', 'info');
        redirect('t/qr-boss/codes');
    }
    $type = array_key_exists(input('type'), QR_TYPES) ? input('type') : $c['type'];
    $url = null;
    if ($type === 'link') {
        $url = qr_clean_url(input('target_url'));
        if (!$url) {
            flash('Γράψε έγκυρο σύνδεσμο.', 'error');
            redirect($here);
        }
    }
    q('UPDATE qr_codes SET name = ?, type = ?, target_url = ?, table_label = ?, active = ? WHERE id = ?', [
        mb_substr(input('name'), 0, 120) ?: $c['name'], $type, $url,
        mb_substr(input('table_label'), 0, 40) ?: null, input('active') === '1' ? 1 : 0, $c['id'],
    ]);
    flash('Αποθηκεύτηκε. Το τυπωμένο QR ανοίγει πλέον το νέο περιεχόμενο.');
    redirect($here);
}

$daily = qr_daily_scans($bid, 14, (int) $c['id']);
$total = (int) qval('SELECT COUNT(*) FROM qr_scans WHERE qr_id = ?', [$c['id']]);
qr_page('code', compact('c', 'daily', 'total'), $c['name'], 'codes');
