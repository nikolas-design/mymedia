<?php
qr_require_edit();
$cat = q1('SELECT * FROM qr_categories WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$cat) {
    not_found();
}
if (is_post()) {
    if (input('action') === 'delete') {
        foreach (qall('SELECT photo FROM qr_items WHERE category_id = ? AND photo IS NOT NULL', [$cat['id']]) as $r) {
            delete_upload($r['photo']);
        }
        q('DELETE FROM qr_categories WHERE id = ?', [$cat['id']]);
        flash('Η κατηγορία διαγράφηκε μαζί με τα πιάτα της.', 'info');
    } else {
        q('UPDATE qr_categories SET name = ?, note = ?, active = ? WHERE id = ?', [
            mb_substr(input('name'), 0, 80) ?: $cat['name'], mb_substr(input('note'), 0, 190) ?: null,
            input('active') === '1' ? 1 : 0, $cat['id'],
        ]);
        flash('Η κατηγορία αποθηκεύτηκε.');
    }
    redirect('t/qr-boss/menu');
}
qr_page('category', compact('cat'), $cat['name'], 'menu');
