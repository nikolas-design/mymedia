<?php
$c = q1('SELECT * FROM ap_customers WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$c) {
    not_found();
}
if (is_post()) {
    $email = input('email');
    q('UPDATE ap_customers SET name = ?, email = ?, notes = ? WHERE id = ?', [mb_substr(input('name'), 0, 120) ?: $c['name'],
        filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null, mb_substr(input('notes'), 0, 500) ?: null, $c['id']]);
    flash('Αποθηκεύτηκε.');
    redirect('t/appointments/customers/' . $c['id']);
}
$bookings = qall('SELECT b.*, s.name AS staff FROM ap_bookings b JOIN ap_staff s ON s.id = b.staff_id WHERE b.customer_id = ? ORDER BY b.starts_at DESC', [$c['id']]);
module_page('customer', compact('c', 'bookings'), $c['name'], 'customers');
