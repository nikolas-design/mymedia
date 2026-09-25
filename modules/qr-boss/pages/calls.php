<?php
if (!$pro) {
    flash('Η κλήση σερβιτόρου είναι διαθέσιμη στο πλάνο Pro.', 'info');
    redirect('t/qr-boss');
}
if (is_post()) {
    $ids = input('action') === 'all'
        ? array_column(qall("SELECT id FROM qr_calls WHERE business_id = ? AND status = 'new'", [$bid]), 'id')
        : [input_int('id')];
    foreach ($ids as $id) {
        q("UPDATE qr_calls SET status = 'done', done_at = NOW(), done_by = ? WHERE id = ? AND business_id = ? AND status = 'new'",
            [$user['id'], $id, $bid]);
    }
    redirect('t/qr-boss/calls');
}
$open = qall("SELECT * FROM qr_calls WHERE business_id = ? AND status = 'new' ORDER BY id", [$bid]);
$done = qall("SELECT c.*, u.name AS by_name FROM qr_calls c LEFT JOIN users u ON u.id = c.done_by
              WHERE c.business_id = ? AND c.status = 'done' AND c.done_at >= NOW() - INTERVAL 3 HOUR ORDER BY c.done_at DESC LIMIT 20", [$bid]);
$lastId = (int) qval('SELECT COALESCE(MAX(id), 0) FROM qr_calls WHERE business_id = ?', [$bid]);
qr_page('calls', compact('open', 'done', 'lastId'), 'Κλήσεις', 'calls');
