<?php
$c = q1('SELECT c.*, u.name AS by_name FROM tm_closings c LEFT JOIN users u ON u.id = c.closed_by WHERE c.id = ? AND c.business_id = ?', [(int) $params[0], $bid]);
if (!$c) {
    not_found();
}
if (is_post() && input('action') === 'delete') {
    module_require_edit();
    q('DELETE FROM tm_closings WHERE id = ?', [$c['id']]);
    flash('Το κλείσιμο διαγράφηκε.', 'info');
    redirect('t/tameio');
}
$expenses = qall('SELECT * FROM tm_expenses WHERE business_id = ? AND day = ? ORDER BY id', [$bid, $c['day']]);
module_page('closing', compact('c', 'expenses'), 'Κλείσιμο ' . date_gr($c['day']), 'overview');
