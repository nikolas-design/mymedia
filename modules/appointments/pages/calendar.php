<?php
$day = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['d'] ?? '')) ? $_GET['d'] : date('Y-m-d');
$staff = qall('SELECT * FROM ap_staff WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
$bookings = qall("SELECT b.*, c.name AS customer, c.phone FROM ap_bookings b JOIN ap_customers c ON c.id = b.customer_id
                  WHERE b.business_id = ? AND b.starts_at >= ? AND b.starts_at < ? AND b.status <> 'cancelled' ORDER BY b.starts_at",
    [$bid, "$day 00:00:00", date('Y-m-d', strtotime("$day +1 day")) . ' 00:00:00']);
$byStaff = [];
foreach ($bookings as $b) {
    $byStaff[(int) $b['staff_id']][] = $b;
}
$pending = qall("SELECT b.*, c.name AS customer FROM ap_bookings b JOIN ap_customers c ON c.id = b.customer_id
                 WHERE b.business_id = ? AND b.status = 'pending' AND b.starts_at >= NOW() ORDER BY b.starts_at", [$bid]);
$week = q1("SELECT COUNT(*) AS n, COALESCE(SUM(price_cents),0) AS rev FROM ap_bookings WHERE business_id = ? AND status IN ('confirmed','done','pending')
            AND starts_at >= ? AND starts_at < ?", [$bid, date('Y-m-d', strtotime('monday this week')), date('Y-m-d', strtotime('monday next week'))]);
$setupNeeded = !$staff || !qval('SELECT 1 FROM ap_services WHERE business_id = ?', [$bid]);
module_page('calendar', compact('day', 'staff', 'byStaff', 'bookings', 'pending', 'week', 'setupNeeded'), 'Ημερολόγιο', 'calendar');
