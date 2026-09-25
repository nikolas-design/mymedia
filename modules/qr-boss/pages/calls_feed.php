<?php
// Για την αυτόματη ανανέωση της οθόνης κλήσεων
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode([
    'open' => $pro ? (int) qval("SELECT COUNT(*) FROM qr_calls WHERE business_id = ? AND status = 'new'", [$bid]) : 0,
    'last' => $pro ? (int) qval('SELECT COALESCE(MAX(id), 0) FROM qr_calls WHERE business_id = ?', [$bid]) : 0,
]);
exit;
