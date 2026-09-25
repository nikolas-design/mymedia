<?php
$search = trim((string) ($_GET['q'] ?? ''));
$args = [$bid];
$where = '';
if ($search !== '') {
    $where = ' AND (c.name LIKE ? OR c.phone LIKE ? OR c.email LIKE ?)';
    array_push($args, "%$search%", "%$search%", "%$search%");
}
$customers = qall("SELECT c.*, COUNT(b.id) AS visits, MAX(b.starts_at) AS last_visit, SUM(b.status = 'noshow') AS noshows
                   FROM ap_customers c LEFT JOIN ap_bookings b ON b.customer_id = c.id AND b.status IN ('done','noshow','confirmed')
                   WHERE c.business_id = ?$where GROUP BY c.id, c.business_id, c.name, c.phone, c.email, c.notes, c.created_at
                   ORDER BY last_visit DESC LIMIT 300", $args);
module_page('customers', compact('customers', 'search'), 'Πελάτες', 'customers');
