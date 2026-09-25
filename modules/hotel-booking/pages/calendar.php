<?php
$from = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['from'] ?? '')) ? $_GET['from'] : date('Y-m-d');
$days = [];
for ($i = 0; $i < 21; $i++) {
    $days[] = date('Y-m-d', strtotime("$from +$i days"));
}
$rooms = qall('SELECT * FROM hb_rooms WHERE business_id = ? AND active = 1 ORDER BY sort, id', [$bid]);
$grid = [];
foreach ($rooms as $r) {
    foreach ($days as $d) {
        $grid[(int) $r['id']][$d] = hb_free_units($r, $d, date('Y-m-d', strtotime("$d +1 day")));
    }
}
$arrivals = qall("SELECT b.*, r.name AS room FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id WHERE b.business_id = ? AND b.checkin = CURDATE() AND b.status IN ('confirmed','pending')", [$bid]);
$departures = qall("SELECT b.*, r.name AS room FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id WHERE b.business_id = ? AND b.checkout = CURDATE() AND b.status IN ('checked_in','confirmed')", [$bid]);
module_page('calendar', compact('from', 'days', 'rooms', 'grid', 'arrivals', 'departures'), 'Διαθεσιμότητα', 'calendar');
