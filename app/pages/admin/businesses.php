<?php
$me = require_admin();

if (is_post()) {
    $name = input('name');
    $email = mb_strtolower(input('owner_email'));
    if (mb_strlen($name) < 2) {
        flash('Γράψε το όνομα της επιχείρησης.', 'error');
        redirect('admin/businesses');
    }
    if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Το email ιδιοκτήτη δεν είναι έγκυρο.', 'error');
        redirect('admin/businesses');
    }
    q('INSERT INTO businesses (name, vat_number, billing_email) VALUES (?, ?, ?)', [$name, input('vat_number') ?: null, $email ?: null]);
    $bid = (int) db()->lastInsertId();
    if ($email !== '') {
        $_SESSION['invite_link'] = create_invitation($bid, $email, 'owner', null, (int) $me['id']);
        flash('Η επιχείρηση δημιουργήθηκε και στάλθηκε πρόσκληση στον ιδιοκτήτη.');
    } else {
        flash('Η επιχείρηση δημιουργήθηκε.');
    }
    redirect('admin/businesses/' . $bid);
}

$search = trim((string) ($_GET['q'] ?? ''));
$sql = "SELECT b.*,
          (SELECT COUNT(*) FROM subscriptions s WHERE s.business_id = b.id AND s.status = 'active') AS subs,
          (SELECT COUNT(*) FROM memberships m WHERE m.business_id = b.id AND m.active = 1) AS members,
          (SELECT COALESCE(SUM(total_cents),0) FROM invoices i WHERE i.business_id = b.id AND i.status = 'issued') AS unpaid
        FROM businesses b";
$args = [];
if ($search !== '') {
    $sql .= ' WHERE b.name LIKE ? OR b.legal_name LIKE ? OR b.vat_number LIKE ?';
    $args = array_fill(0, 3, '%' . $search . '%');
}
$businesses = qall($sql . ' ORDER BY b.name', $args);
render('admin/businesses', compact('businesses', 'search'), ['title' => 'Επιχειρήσεις', 'nav' => 'admin-businesses', 'area' => 'admin']);
