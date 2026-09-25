<?php
require_admin();

$subs = qall("SELECT s.*, t.name AS tool_name FROM subscriptions s JOIN tools t ON t.id = s.tool_id WHERE s.status = 'active'");
$mrr = array_sum(array_map('monthly_cents', $subs));
$byTool = [];
foreach ($subs as $s) {
    $byTool[$s['tool_name']] = ($byTool[$s['tool_name']] ?? 0) + monthly_cents($s);
}
arsort($byTool);

$businessCount = (int) qval('SELECT COUNT(*) FROM businesses');
$unpaid = (int) qval("SELECT COALESCE(SUM(total_cents),0) FROM invoices WHERE status = 'issued'");
$requests = qall("SELECT r.*, b.name AS business_name, t.name AS tool_name, p.name AS plan_name, p.price_cents, p.period
                  FROM tool_requests r JOIN businesses b ON b.id = r.business_id JOIN tools t ON t.id = r.tool_id
                  LEFT JOIN plans p ON p.id = r.plan_id
                  WHERE r.status IN ('new','setup') ORDER BY r.created_at DESC LIMIT 10");
$tickets = qall("SELECT t.*, b.name AS business_name FROM tickets t JOIN businesses b ON b.id = t.business_id
                 WHERE t.status = 'open' ORDER BY t.updated_at DESC LIMIT 6");

// Νέες επιχειρήσεις ανά μήνα, τελευταίοι 6 μήνες
$months = [];
for ($i = 5; $i >= 0; $i--) {
    $months[date('Y-m', strtotime("first day of -$i month"))] = 0;
}
foreach (qall("SELECT DATE_FORMAT(created_at, '%Y-%m') AS ym, COUNT(*) AS n FROM businesses
               WHERE created_at >= ? GROUP BY ym", [array_key_first($months) . '-01']) as $r) {
    $months[$r['ym']] = (int) $r['n'];
}

render('admin/overview', compact('mrr', 'byTool', 'businessCount', 'unpaid', 'requests', 'tickets', 'months'),
    ['title' => 'Διαχείριση', 'nav' => 'admin', 'area' => 'admin']);
