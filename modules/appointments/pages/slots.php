<?php
// JSON: ελεύθερες ώρες για τη φόρμα νέου ραντεβού
header('Content-Type: application/json; charset=utf-8');
$service = q1('SELECT * FROM ap_services WHERE id = ? AND business_id = ?', [(int) ($_GET['service'] ?? 0), $bid]);
$staffId = (int) ($_GET['staff'] ?? 0);
$day = (string) ($_GET['day'] ?? '');
if (!$service || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || !qval('SELECT 1 FROM ap_staff WHERE id = ? AND business_id = ?', [$staffId, $bid])) {
    echo '[]';
    exit;
}
echo json_encode(ap_free_slots($settings, $staffId, $day, (int) $service['duration_min'], false, (int) ($_GET['ignore'] ?? 0)));
exit;
