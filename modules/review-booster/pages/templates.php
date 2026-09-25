<?php
if (!$pro) {
    redirect('t/review-booster');
}
if (!qval('SELECT 1 FROM rb_templates WHERE business_id = ?', [$bid])) {
    foreach (RB_DEFAULT_TEMPLATES as [$title, $kind, $body]) {
        q('INSERT INTO rb_templates (business_id, title, kind, body) VALUES (?, ?, ?, ?)', [$bid, $title, $kind, $body]);
    }
}
if (is_post()) {
    module_require_edit();
    if (input('action') === 'delete') {
        q('DELETE FROM rb_templates WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
    } else {
        $kind = in_array(input('kind'), ['positive', 'neutral', 'negative'], true) ? input('kind') : 'positive';
        $data = [mb_substr(input('title'), 0, 120) ?: 'Πρότυπο', $kind, input('body')];
        if (input_int('id')) {
            q('UPDATE rb_templates SET title = ?, kind = ?, body = ? WHERE id = ? AND business_id = ?', [...$data, input_int('id'), $bid]);
        } elseif (input('body') !== '') {
            q('INSERT INTO rb_templates (title, kind, body, business_id) VALUES (?, ?, ?, ?)', [...$data, $bid]);
        }
        flash('Αποθηκεύτηκε.');
    }
    redirect('t/review-booster/templates');
}
$templates = qall("SELECT * FROM rb_templates WHERE business_id = ? ORDER BY FIELD(kind, 'positive', 'neutral', 'negative'), id", [$bid]);
$phone = (string) qval('SELECT phone FROM businesses WHERE id = ?', [$bid]);
module_page('templates', compact('templates', 'phone'), 'Πρότυπα απαντήσεων', 'templates');
