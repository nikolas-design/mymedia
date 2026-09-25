<?php
$sup = (int) ($_GET['supplier'] ?? 0);
$args = [$bid];
$where = '';
if ($sup) {
    $where = ' AND o.supplier_id = ?';
    $args[] = $sup;
}
$orders = qall("SELECT o.*, s.name AS supplier, (SELECT COUNT(*) FROM of_order_lines l WHERE l.order_id = o.id) AS n
                FROM of_orders o JOIN of_suppliers s ON s.id = o.supplier_id WHERE o.business_id = ?$where ORDER BY o.id DESC LIMIT 200", $args);
$suppliers = qall('SELECT id, name FROM of_suppliers WHERE business_id = ? ORDER BY name', [$bid]);
$bySupplier = qall("SELECT s.name, SUM(o.total_cents) AS total, COUNT(*) AS n FROM of_orders o JOIN of_suppliers s ON s.id = o.supplier_id
                    WHERE o.business_id = ? AND o.status IN ('sent','received') AND o.created_at >= CURDATE() - INTERVAL 29 DAY
                    GROUP BY s.id, s.name ORDER BY total DESC", [$bid]);
module_page('orders', compact('orders', 'suppliers', 'sup', 'bySupplier'), 'Ιστορικό', 'orders');
