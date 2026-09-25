<?php
$day = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['d'] ?? '')) ? $_GET['d'] : date('Y-m-d');
$orders = qall('SELECT * FROM ro_orders WHERE business_id = ? AND created_at >= ? AND created_at < ? ORDER BY id DESC', [$bid, "$day 00:00:00", date('Y-m-d', strtotime("$day +1 day"))]);
$daily = empty_days(14);
foreach (qall("SELECT DATE(created_at) AS d, SUM(total_cents) AS t FROM ro_orders WHERE business_id = ? AND status IN ('accepted','ready','out','completed') AND created_at >= ? GROUP BY DATE(created_at)", [$bid, array_key_first($daily)]) as $r) {
    $daily[$r['d']] = (int) round($r['t'] / 100);
}
$top = qall("SELECT l.name, SUM(l.qty) AS q FROM ro_order_lines l JOIN ro_orders o ON o.id = l.order_id WHERE o.business_id = ? AND o.status IN ('accepted','ready','out','completed') AND o.created_at >= CURDATE() - INTERVAL 29 DAY GROUP BY l.name ORDER BY q DESC LIMIT 8", [$bid]);
module_page('history', compact('day', 'orders', 'daily', 'top'), 'Ιστορικό', 'history');
