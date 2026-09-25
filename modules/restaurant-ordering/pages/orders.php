<?php
$active = qall("SELECT * FROM ro_orders WHERE business_id = ? AND status IN ('new','accepted','ready','out') ORDER BY status = 'new' DESC, id", [$bid]);
$lines = [];
if ($active) {
    foreach (qall('SELECT * FROM ro_order_lines WHERE order_id IN (' . implode(',', array_map('intval', array_column($active, 'id'))) . ') ORDER BY id') as $l) {
        $lines[(int) $l['order_id']][] = $l;
    }
}
$today = q1("SELECT COUNT(*) AS n, COALESCE(SUM(total_cents),0) AS total FROM ro_orders WHERE business_id = ? AND status IN ('accepted','ready','out','completed') AND created_at >= CURDATE()", [$bid]);
$lastId = (int) qval('SELECT COALESCE(MAX(id),0) FROM ro_orders WHERE business_id = ?', [$bid]);
module_page('orders', compact('active', 'lines', 'today', 'lastId', 'settings'), 'Παραγγελίες', 'orders');
