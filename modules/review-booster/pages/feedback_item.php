<?php
$f = q1('SELECT f.*, l.name AS location FROM rb_feedback f JOIN rb_locations l ON l.id = f.location_id WHERE f.id = ? AND f.business_id = ?',
    [(int) $params[0], $bid]);
if (!$f) {
    not_found();
}
if (is_post()) {
    $resolved = input('status') === 'resolved';
    q('UPDATE rb_feedback SET note = ?, status = ?, resolved_at = ? WHERE id = ?',
        [input('note') ?: null, $resolved ? 'resolved' : 'new', $resolved ? date('Y-m-d H:i:s') : null, $f['id']]);
    flash($resolved ? 'Σημειώθηκε ως λυμένο.' : 'Αποθηκεύτηκε.');
    redirect('t/review-booster/feedback');
}
module_page('feedback_item', compact('f'), 'Σχόλιο', 'feedback');
