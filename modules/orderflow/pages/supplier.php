<?php
$isNew = $params[0] === 'new';
$sup = $isNew ? null : q1('SELECT * FROM of_suppliers WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$sup) {
    not_found();
}
if (is_post()) {
    module_require_edit();
    $action = input('action');
    if ($action === 'supplier') {
        $email = input('email');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('Το email δεν είναι έγκυρο.', 'error');
            redirect('t/orderflow/suppliers/' . ($isNew ? 'new' : $sup['id']));
        }
        $data = [mb_substr(input('name'), 0, 120) ?: 'Προμηθευτής', input('contact_name') ?: null, input('phone') ?: null, $email ?: null,
            mb_substr(input('order_days'), 0, 120) ?: null, mb_substr(input('notes'), 0, 255) ?: null];
        if ($isNew) {
            q('INSERT INTO of_suppliers (name, contact_name, phone, email, order_days, notes, business_id) VALUES (?, ?, ?, ?, ?, ?, ?)', [...$data, $bid]);
            flash('Ο προμηθευτής προστέθηκε. Βάλε τώρα τα είδη του.');
            redirect('t/orderflow/suppliers/' . db()->lastInsertId());
        }
        q('UPDATE of_suppliers SET name = ?, contact_name = ?, phone = ?, email = ?, order_days = ?, notes = ?, active = ? WHERE id = ?',
            [...$data, input('active') === '1' ? 1 : 0, $sup['id']]);
        flash('Αποθηκεύτηκε.');
    } elseif (!$isNew && $action === 'products') {
        // Μαζική αποθήκευση ειδών (υπάρχοντα + νέες γραμμές)
        foreach ((array) ($_POST['p'] ?? []) as $id => $row) {
            $name = mb_substr(trim((string) ($row['name'] ?? '')), 0, 120);
            $unit = mb_substr(trim((string) ($row['unit'] ?? '')), 0, 30) ?: 'τεμ.';
            $price = trim((string) ($row['price'] ?? '')) === '' ? null : parse_money((string) $row['price']);
            if (str_starts_with((string) $id, 'n')) {
                if ($name !== '') {
                    q('INSERT INTO of_products (business_id, supplier_id, name, unit, price_cents, sort) VALUES (?, ?, ?, ?, ?, ?)',
                        [$bid, $sup['id'], $name, $unit, $price, 1000]);
                }
            } elseif (!empty($row['delete'])) {
                q('DELETE FROM of_products WHERE id = ? AND supplier_id = ?', [(int) $id, $sup['id']]);
            } elseif ($name !== '') {
                q('UPDATE of_products SET name = ?, unit = ?, price_cents = ? WHERE id = ? AND supplier_id = ?', [$name, $unit, $price, (int) $id, $sup['id']]);
            }
        }
        flash('Τα είδη αποθηκεύτηκαν.');
    } elseif (!$isNew && $action === 'delete') {
        q('DELETE FROM of_suppliers WHERE id = ?', [$sup['id']]);
        flash('Ο προμηθευτής διαγράφηκε.', 'info');
        redirect('t/orderflow/suppliers');
    }
    redirect('t/orderflow/suppliers/' . $sup['id']);
}
$products = $isNew ? [] : qall('SELECT * FROM of_products WHERE supplier_id = ? ORDER BY sort, name', [$sup['id']]);
module_page('supplier', compact('sup', 'products', 'isNew'), $isNew ? 'Νέος προμηθευτής' : $sup['name'], 'suppliers');
