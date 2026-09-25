<?php
// Το βλέπουν ο ιδιοκτήτης της επιχείρησης και οι admin
require_login();
$inv = q1('SELECT i.*, b.name AS business_name, b.legal_name, b.vat_number, b.tax_office, b.address
           FROM invoices i JOIN businesses b ON b.id = i.business_id WHERE i.id = ?', [(int) $params[0]]);
if (!$inv) {
    not_found();
}
$admin = is_admin();
if (!$admin) {
    $biz = current_business();
    if (!$biz || (int) $biz['id'] !== (int) $inv['business_id'] || $biz['role'] !== 'owner') {
        not_found();
    }
}
$lines = qall('SELECT * FROM invoice_lines WHERE invoice_id = ? ORDER BY id', [$inv['id']]);
render('invoice', compact('inv', 'lines', 'admin'), [
    'title' => $inv['number'],
    'nav'   => $admin && !current_business() ? 'admin-invoices' : 'billing',
    'area'  => $admin && !current_business() ? 'admin' : 'client',
]);
