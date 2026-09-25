<?php
$today = date('Y-m-d');
$todayClosings = qall('SELECT c.*, u.name AS by_name FROM tm_closings c LEFT JOIN users u ON u.id = c.closed_by WHERE c.business_id = ? AND c.day = ? ORDER BY c.id', [$bid, $today]);
$todayExpenses = qall('SELECT * FROM tm_expenses WHERE business_id = ? AND day = ? ORDER BY id DESC', [$bid, $today]);
$stats = null;
$daily = empty_days(14);
if ($canEdit) {
    $sum = fn(string $from) => q1('SELECT COALESCE(SUM(cash_cents + card_cents + other_cents),0) AS sales FROM tm_closings WHERE business_id = ? AND day >= ?', [$bid, $from])['sales'];
    $exp = fn(string $from) => (int) qval('SELECT COALESCE(SUM(amount_cents),0) FROM tm_expenses WHERE business_id = ? AND day >= ?', [$bid, $from]);
    $weekFrom = date('Y-m-d', strtotime('monday this week'));
    $monthFrom = date('Y-m-01');
    $stats = ['week' => (int) $sum($weekFrom), 'month' => (int) $sum($monthFrom), 'month_exp' => $exp($monthFrom)];
    foreach (qall('SELECT day, SUM(cash_cents + card_cents + other_cents) AS s FROM tm_closings WHERE business_id = ? AND day >= ? GROUP BY day',
        [$bid, array_key_first($daily)]) as $r) {
        $daily[$r['day']] = (int) round($r['s'] / 100);
    }
}
module_page('overview', compact('todayClosings', 'todayExpenses', 'stats', 'daily'), 'Σήμερα', 'overview');
