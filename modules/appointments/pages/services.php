<?php
module_require_edit();
if (is_post()) {
    $id = input_int('id');
    if (input('action') === 'delete') {
        q('DELETE FROM ap_services WHERE id = ? AND business_id = ?', [$id, $bid]);
        flash('Η υπηρεσία διαγράφηκε.', 'info');
        redirect('t/appointments/services');
    }
    $data = [mb_substr(input('name'), 0, 120), mb_substr(input('description'), 0, 255) ?: null, max(5, min(600, input_int('duration_min'))),
        input('price') === '' ? null : parse_money(input('price')), input('active') === '1' || !$id ? 1 : 0];
    if ($data[0] === '') {
        flash('Γράψε όνομα υπηρεσίας.', 'error');
    } elseif ($id) {
        q('UPDATE ap_services SET name = ?, description = ?, duration_min = ?, price_cents = ?, active = ? WHERE id = ? AND business_id = ?', [...$data, $id, $bid]);
        flash('Αποθηκεύτηκε.');
    } else {
        q('INSERT INTO ap_services (name, description, duration_min, price_cents, active, business_id, sort) VALUES (?, ?, ?, ?, ?, ?, ?)',
            [...$data, $bid, (int) qval('SELECT COALESCE(MAX(sort),0)+10 FROM ap_services WHERE business_id = ?', [$bid])]);
        $sid = (int) db()->lastInsertId();
        // Νέα υπηρεσία: την κάνουν όλοι οι συνεργάτες, μέχρι να αλλάξει
        foreach (qall('SELECT id FROM ap_staff WHERE business_id = ?', [$bid]) as $s) {
            q('INSERT IGNORE INTO ap_staff_services (staff_id, service_id) VALUES (?, ?)', [$s['id'], $sid]);
        }
        flash('Η υπηρεσία προστέθηκε.');
    }
    redirect('t/appointments/services');
}
$services = qall('SELECT * FROM ap_services WHERE business_id = ? ORDER BY active DESC, sort, name', [$bid]);
module_page('services', compact('services'), 'Υπηρεσίες', 'services');
