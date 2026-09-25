<?php
rb_ensure_location($bid, $business['name']);
$s30 = rb_summary($bid, date('Y-m-d', strtotime('-29 days')));
$daily = empty_days(14);
foreach (qall('SELECT DATE(created_at) AS d, COUNT(*) AS n FROM rb_ratings WHERE business_id = ? AND created_at >= ? GROUP BY DATE(created_at)',
    [$bid, array_key_first($daily)]) as $r) {
    $daily[$r['d']] = (int) $r['n'];
}
$recent = qall("SELECT f.*, l.name AS location FROM rb_feedback f JOIN rb_locations l ON l.id = f.location_id
                WHERE f.business_id = ? AND f.status = 'new' ORDER BY f.id DESC LIMIT 4", [$bid]);
$noGoogle = qall("SELECT id, name FROM rb_locations WHERE business_id = ? AND active = 1 AND (google_url IS NULL OR google_url = '')", [$bid]);
module_page('overview', compact('s30', 'daily', 'recent', 'noGoogle'), 'Επισκόπηση', 'overview');
