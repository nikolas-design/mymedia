<?php
$biz = require_business();
require_role('owner', 'manager');

$fields = ['name', 'legal_name', 'vat_number', 'tax_office', 'address', 'phone', 'billing_email'];
if (is_post()) {
    $v = [];
    foreach ($fields as $f) {
        $v[$f] = mb_substr(input($f), 0, 190) ?: null;
    }
    if (!$v['name']) {
        flash('Το όνομα της επιχείρησης είναι υποχρεωτικό.', 'error');
        redirect('business');
    }
    if ($v['vat_number'] && !preg_match('/^\d{9}$/', $v['vat_number'])) {
        flash('Το ΑΦΜ πρέπει να έχει 9 ψηφία.', 'error');
        redirect('business');
    }
    if ($v['billing_email'] && !filter_var($v['billing_email'], FILTER_VALIDATE_EMAIL)) {
        flash('Το email τιμολόγησης δεν είναι έγκυρο.', 'error');
        redirect('business');
    }
    q('UPDATE businesses SET name = ?, legal_name = ?, vat_number = ?, tax_office = ?, address = ?, phone = ?, billing_email = ? WHERE id = ?',
        [...array_values($v), $biz['id']]);
    flash('Τα στοιχεία αποθηκεύτηκαν.');
    redirect('business');
}
$b = q1('SELECT * FROM businesses WHERE id = ?', [$biz['id']]);
render('business', ['b' => $b], ['title' => 'Ρυθμίσεις επιχείρησης', 'nav' => 'business']);
