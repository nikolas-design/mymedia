<?php
$x = q1('SELECT x.*, u.name AS by_name FROM tm_expenses x LEFT JOIN users u ON u.id = x.created_by WHERE x.id = ? AND x.business_id = ?', [(int) $params[0], $bid]);
if (!$x) {
    not_found();
}
if (is_post() && input('action') === 'delete') {
    // Διαγράφει ο ιδιοκτήτης/υπεύθυνος ή όποιος το καταχώρησε, την ίδια μέρα
    if (!$canEdit && !((int) $x['created_by'] === (int) $user['id'] && $x['day'] === date('Y-m-d'))) {
        forbidden();
    }
    delete_upload($x['photo']);
    q('DELETE FROM tm_expenses WHERE id = ?', [$x['id']]);
    flash('Το έξοδο διαγράφηκε.', 'info');
    redirect('t/tameio/expenses');
}
module_page('expense', compact('x'), 'Έξοδο', 'expenses');
