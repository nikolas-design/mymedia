<?php
$status = array_key_exists($_GET['status'] ?? '', HB_STATUS) ? $_GET['status'] : '';
$args = [$bid];
$where = $status ? ' AND b.status = ?' : " AND b.checkout >= CURDATE() AND b.status <> 'cancelled'";
if ($status) {
    $args[] = $status;
}
$bookings = qall("SELECT b.*, r.name AS room FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id WHERE b.business_id = ?$where ORDER BY b.checkin LIMIT 300", $args);
module_page('bookings', compact('bookings', 'status'), 'Κρατήσεις', 'bookings');
