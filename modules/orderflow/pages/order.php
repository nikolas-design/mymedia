<?php
$o = q1('SELECT o.*, s.name AS supplier, s.email, s.phone, s.contact_name, u.name AS by_name FROM of_orders o
         JOIN of_suppliers s ON s.id = o.supplier_id LEFT JOIN users u ON u.id = o.created_by
         WHERE o.id = ? AND o.business_id = ?', [(int) $params[0], $bid]);
if (!$o) {
    not_found();
}
$lines = qall('SELECT * FROM of_order_lines WHERE order_id = ? ORDER BY id', [$o['id']]);
$text = of_order_text($o, $lines, $business['name']);
$here = 't/orderflow/orders/' . $o['id'];

if (is_post()) {
    $do = input('do');
    if ($do === 'email' && $o['status'] === 'draft') {
        if (!$o['email']) {
            flash('Ο προμηθευτής δεν έχει email.', 'error');
            redirect($here);
        }
        $ok = send_mail($o['email'], 'Παραγγελία από ' . $business['name'], $text);
        if (!$ok) {
            flash('Το email δεν έφυγε. Στείλε την παραγγελία με Viber ή WhatsApp.', 'error');
            redirect($here);
        }
        q("UPDATE of_orders SET status = 'sent', sent_via = 'email', sent_at = NOW() WHERE id = ?", [$o['id']]);
        flash('Η παραγγελία στάλθηκε στο ' . $o['email'] . '.');
    } elseif (in_array($do, ['viber', 'whatsapp', 'phone', 'other'], true) && $o['status'] === 'draft') {
        q("UPDATE of_orders SET status = 'sent', sent_via = ?, sent_at = NOW() WHERE id = ?", [$do, $o['id']]);
        flash('Σημειώθηκε ως σταλμένη.');
    } elseif ($do === 'received' && $o['status'] === 'sent') {
        q("UPDATE of_orders SET status = 'received', received_at = NOW() WHERE id = ?", [$o['id']]);
        flash('Η παραγγελία παραλήφθηκε.');
    } elseif ($do === 'cancel' && in_array($o['status'], ['draft', 'sent'], true)) {
        q("UPDATE of_orders SET status = 'cancelled' WHERE id = ?", [$o['id']]);
        flash('Η παραγγελία ακυρώθηκε.', 'info');
    } elseif ($do === 'delete' && $o['status'] === 'draft') {
        q('DELETE FROM of_orders WHERE id = ?', [$o['id']]);
        redirect('t/orderflow');
    }
    redirect($here);
}
$phone = preg_replace('/\D/', '', (string) $o['phone']);
if ($phone && strlen($phone) === 10) {
    $phone = '30' . $phone; // ελληνικό κινητό χωρίς κωδικό χώρας
}
module_page('order', compact('o', 'lines', 'text', 'phone'), 'Παραγγελία #' . $o['id'], 'orders');
