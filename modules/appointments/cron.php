<?php
declare(strict_types=1);

// Appointments: υπενθύμιση με email ~24 ώρες πριν από κάθε ραντεβού
require_once __DIR__ . '/lib.php';

$due = qall("SELECT b.id FROM ap_bookings b JOIN ap_customers c ON c.id = b.customer_id
             WHERE b.status = 'confirmed' AND b.reminded_at IS NULL AND c.email IS NOT NULL
             AND b.starts_at BETWEEN NOW() + INTERVAL 2 HOUR AND NOW() + INTERVAL 26 HOUR LIMIT 200");
foreach ($due as $r) {
    ap_mail_customer((int) $r['id'], 'reminder');
    q('UPDATE ap_bookings SET reminded_at = NOW() WHERE id = ?', [$r['id']]);
}
return count($due) . ' υπενθυμίσεις';
