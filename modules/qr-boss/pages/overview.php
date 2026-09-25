<?php
$daily = qr_daily_scans($bid, 14);
$stats = q1("SELECT
    SUM(scanned_at >= CURDATE()) AS today,
    SUM(scanned_at >= CURDATE() - INTERVAL 6 DAY) AS week,
    COUNT(*) AS month
  FROM qr_scans WHERE business_id = ? AND scanned_at >= CURDATE() - INTERVAL 29 DAY", [$bid]);
$top = qall("SELECT c.*, (SELECT COUNT(*) FROM qr_scans s WHERE s.qr_id = c.id AND s.scanned_at >= CURDATE() - INTERVAL 29 DAY) AS n
             FROM qr_codes c WHERE c.business_id = ? ORDER BY n DESC, c.id LIMIT 5", [$bid]);
$codeCount = (int) qval('SELECT COUNT(*) FROM qr_codes WHERE business_id = ? AND active = 1', [$bid]);
$itemCount = (int) qval('SELECT COUNT(*) FROM qr_items WHERE business_id = ?', [$bid]);
$calls = $pro ? qall("SELECT * FROM qr_calls WHERE business_id = ? AND status = 'new' ORDER BY id DESC LIMIT 5", [$bid]) : [];

qr_page('overview', compact('daily', 'stats', 'top', 'codeCount', 'itemCount', 'calls'),
    'Επισκόπηση', 'overview');
