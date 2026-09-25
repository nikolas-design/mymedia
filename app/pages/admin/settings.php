<?php
require_admin();
require_once APP_ROOT . '/app/migrate.php';

$keys = [
    'company_name'     => 'Επωνυμία (στα παραστατικά)',
    'bank_name'        => 'Τράπεζα',
    'bank_iban'        => 'IBAN',
    'bank_beneficiary' => 'Δικαιούχος',
    'invoice_due_days' => 'Ημέρες προθεσμίας πληρωμής',
    'support_email'    => 'Email υποστήριξης',
];

if (is_post()) {
    if (input('action') === 'migrate') {
        $ran = run_migrations();
        flash($ran ? 'Εφαρμόστηκαν: ' . implode(', ', $ran) : 'Η βάση είναι ήδη ενημερωμένη.');
    } else {
        foreach ($keys as $k => $_) {
            q('INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)', [$k, input($k)]);
        }
        flash('Οι ρυθμίσεις αποθηκεύτηκαν.');
    }
    redirect('admin/settings');
}

$pending = array_keys(pending_migrations());
render('admin/settings', compact('keys', 'pending'), ['title' => 'Ρυθμίσεις', 'nav' => 'admin-settings', 'area' => 'admin']);
