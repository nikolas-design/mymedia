<?php
// Εβδομαδιαία αναφορά φήμης: αυτή η εβδομάδα σε σύγκριση με την προηγούμενη
$thisFrom = date('Y-m-d', strtotime('-6 days'));
$prevFrom = date('Y-m-d', strtotime('-13 days'));
$now = rb_summary($bid, $thisFrom);
$prev = rb_summary($bid, $prevFrom, $thisFrom);
$byLocation = qall('SELECT l.name, COUNT(r.id) AS n, AVG(r.stars) AS avg, SUM(r.went_google) AS google FROM rb_locations l
                    LEFT JOIN rb_ratings r ON r.location_id = l.id AND r.created_at >= ?
                    WHERE l.business_id = ? GROUP BY l.id, l.name ORDER BY l.id', [$thisFrom, $bid]);
$feedback = qall('SELECT * FROM rb_feedback WHERE business_id = ? AND created_at >= ? ORDER BY id DESC', [$bid, $thisFrom]);
module_page('report', compact('now', 'prev', 'byLocation', 'feedback', 'thisFrom'), 'Αναφορά', 'report');
