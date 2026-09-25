<?php
$sup = q1('SELECT * FROM of_suppliers WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$sup) {
    not_found();
}
$products = qall('SELECT * FROM of_products WHERE supplier_id = ? AND active = 1 ORDER BY sort, name', [$sup['id']]);

if (is_post()) {
    $lines = [];
    foreach ($products as $p) {
        $qty = (float) str_replace(',', '.', (string) ($_POST['qty'][$p['id']] ?? '0'));
        if ($qty > 0) {
            $lines[] = ['product_id' => $p['id'], 'name' => $p['name'], 'unit' => $p['unit'], 'qty' => min($qty, 99999), 'price_cents' => $p['price_cents']];
        }
    }
    // Είδος εκτός καταλόγου
    if (input('extra_name') !== '' && (float) str_replace(',', '.', input('extra_qty')) > 0) {
        $lines[] = ['product_id' => null, 'name' => mb_substr(input('extra_name'), 0, 120), 'unit' => mb_substr(input('extra_unit') ?: 'τεμ.', 0, 30),
            'qty' => (float) str_replace(',', '.', input('extra_qty')), 'price_cents' => null];
    }
    if (!$lines) {
        flash('Βάλε ποσότητα σε τουλάχιστον ένα είδος.', 'error');
        redirect('t/orderflow/new/' . $sup['id']);
    }
    $delivery = preg_match('/^\d{4}-\d{2}-\d{2}$/', input('delivery_date')) ? input('delivery_date') : null;
    q('INSERT INTO of_orders (business_id, supplier_id, delivery_date, note, total_cents, created_by) VALUES (?, ?, ?, ?, ?, ?)',
        [$bid, $sup['id'], $delivery, mb_substr(input('note'), 0, 500) ?: null, of_order_total($lines), $user['id']]);
    $oid = (int) db()->lastInsertId();
    foreach ($lines as $l) {
        q('INSERT INTO of_order_lines (order_id, product_id, name, unit, qty, price_cents) VALUES (?, ?, ?, ?, ?, ?)',
            [$oid, $l['product_id'], $l['name'], $l['unit'], $l['qty'], $l['price_cents']]);
    }
    redirect('t/orderflow/orders/' . $oid);
}

// Προσυμπλήρωση από προηγούμενη παραγγελία (Επανάληψη)
$prefill = [];
if (!empty($_GET['from'])) {
    foreach (qall('SELECT l.product_id, l.qty FROM of_order_lines l JOIN of_orders o ON o.id = l.order_id WHERE o.id = ? AND o.business_id = ?',
        [(int) $_GET['from'], $bid]) as $l) {
        $prefill[(int) $l['product_id']] = (float) $l['qty'];
    }
}
$last = qall("SELECT l.product_id, l.qty FROM of_order_lines l WHERE l.order_id =
              (SELECT id FROM of_orders WHERE supplier_id = ? AND status IN ('sent','received') ORDER BY id DESC LIMIT 1)", [$sup['id']]);
$lastQty = array_column($last, 'qty', 'product_id');
module_page('order_new', compact('sup', 'products', 'prefill', 'lastQty'), 'Παραγγελία σε ' . $sup['name'], 'overview');
