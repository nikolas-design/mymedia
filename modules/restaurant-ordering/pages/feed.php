<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store');
echo json_encode(['last' => (int) qval('SELECT COALESCE(MAX(id),0) FROM ro_orders WHERE business_id = ?', [$bid])]);
exit;
