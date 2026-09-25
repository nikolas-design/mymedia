<?php
module_require_edit();
$month = preg_match('/^\d{4}-\d{2}$/', (string) ($_GET['m'] ?? '')) ? $_GET['m'] : date('Y-m');
$from = $month . '-01';
$to = date('Y-m-01', strtotime($from . ' +1 month'));
$days = [];
foreach (qall('SELECT day, SUM(cash_cents) AS cash, SUM(card_cents) AS card, SUM(other_cents) AS other,
                      SUM(COALESCE(counted_cents, 0) - (opening_cents + cash_cents - cash_expenses_cents)) AS diff, SUM(counted_cents IS NOT NULL) AS counted
               FROM tm_closings WHERE business_id = ? AND day >= ? AND day < ? GROUP BY day', [$bid, $from, $to]) as $r) {
    $days[$r['day']] = $r + ['exp' => 0];
}
foreach (qall('SELECT day, SUM(amount_cents) AS exp FROM tm_expenses WHERE business_id = ? AND day >= ? AND day < ? GROUP BY day', [$bid, $from, $to]) as $r) {
    $days[$r['day']] = ($days[$r['day']] ?? ['day' => $r['day'], 'cash' => 0, 'card' => 0, 'other' => 0, 'diff' => 0, 'counted' => 0]);
    $days[$r['day']]['exp'] = (int) $r['exp'];
}
krsort($days);
$byCat = qall('SELECT category, SUM(amount_cents) AS total FROM tm_expenses WHERE business_id = ? AND day >= ? AND day < ? GROUP BY category ORDER BY total DESC', [$bid, $from, $to]);
module_page('report', compact('month', 'days', 'byCat'), 'Αναφορές', 'report');
