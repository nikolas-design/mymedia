<?php
module_require_edit();
if (is_post()) {
    $action = input('action');
    if ($action === 'category' && input('name') !== '') {
        q('INSERT INTO ro_categories (business_id, name, sort) VALUES (?, ?, ?)', [$bid, mb_substr(input('name'), 0, 80), (int) qval('SELECT COALESCE(MAX(sort),0)+10 FROM ro_categories WHERE business_id = ?', [$bid])]);
    } elseif ($action === 'delete_category') {
        q('DELETE FROM ro_categories WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
    } elseif ($action === 'item') {
        $id = input_int('id');
        $cat = q1('SELECT id FROM ro_categories WHERE id = ? AND business_id = ?', [input_int('category_id'), $bid]);
        if (!$cat || input('name') === '' || input('price') === '') {
            flash('Συμπλήρωσε όνομα και τιμή.', 'error');
            redirect('t/restaurant-ordering/menu');
        }
        $data = [$cat['id'], mb_substr(input('name'), 0, 120), mb_substr(input('description'), 0, 400) ?: null, parse_money(input('price')), input('available') === '1' || !$id ? 1 : 0];
        if ($id) {
            q('UPDATE ro_items SET category_id = ?, name = ?, description = ?, price_cents = ?, available = ? WHERE id = ? AND business_id = ?', [...$data, $id, $bid]);
        } else {
            q('INSERT INTO ro_items (category_id, name, description, price_cents, available, business_id, sort) VALUES (?, ?, ?, ?, ?, ?, ?)', [...$data, $bid, (int) qval('SELECT COALESCE(MAX(sort),0)+10 FROM ro_items WHERE category_id = ?', [$cat['id']])]);
        }
    } elseif ($action === 'delete_item') {
        q('DELETE FROM ro_items WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
    } elseif ($action === 'toggle') {
        q('UPDATE ro_items SET available = 1 - available WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
    } elseif ($action === 'import' && !qval('SELECT 1 FROM ro_categories WHERE business_id = ?', [$bid])) {
        // Εισαγωγή από το μενού του QR Boss (όσα έχουν τιμή)
        $n = 0;
        foreach (qall('SELECT * FROM qr_categories WHERE business_id = ? AND active = 1 ORDER BY sort, id', [$bid]) as $c) {
            q('INSERT INTO ro_categories (business_id, name, sort) VALUES (?, ?, ?)', [$bid, $c['name'], $c['sort']]);
            $cid = (int) db()->lastInsertId();
            foreach (qall('SELECT * FROM qr_items WHERE category_id = ? AND price_cents IS NOT NULL ORDER BY sort, id', [$c['id']]) as $i) {
                q('INSERT INTO ro_items (business_id, category_id, name, description, price_cents, photo, available, sort) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
                    [$bid, $cid, $i['name'], $i['description'], $i['price_cents'], $i['photo'], $i['available'], $i['sort']]);
                $n++;
            }
        }
        flash($n ? "Εισήχθησαν $n πιάτα από το μενού του QR Boss." : 'Δεν βρέθηκε μενού στο QR Boss.', $n ? 'ok' : 'info');
    }
    redirect('t/restaurant-ordering/menu');
}
$categories = qall('SELECT * FROM ro_categories WHERE business_id = ? ORDER BY sort, id', [$bid]);
$items = [];
foreach (qall('SELECT * FROM ro_items WHERE business_id = ? ORDER BY sort, id', [$bid]) as $i) {
    $items[(int) $i['category_id']][] = $i;
}
$hasQrMenu = (bool) qval('SELECT 1 FROM qr_items WHERE business_id = ?', [$bid]);
$edit = (int) ($_GET['edit'] ?? 0);
module_page('menu', compact('categories', 'items', 'hasQrMenu', 'edit'), 'Κατάλογος', 'menu');
