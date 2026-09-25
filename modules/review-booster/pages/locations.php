<?php
rb_ensure_location($bid, $business['name']);
$limit = rb_location_limit($subscription);
if (is_post()) {
    module_require_edit();
    $count = (int) qval('SELECT COUNT(*) FROM rb_locations WHERE business_id = ?', [$bid]);
    if ($count >= $limit) {
        flash('Το πλάνο σου περιλαμβάνει έως ' . $limit . ($limit === 1 ? ' σημείο.' : ' σημεία.') . ' Για περισσότερα, αναβάθμιση σε Pro.', 'error');
        redirect('t/review-booster/locations');
    }
    do {
        $code = short_code();
    } while (qval('SELECT 1 FROM rb_locations WHERE code = ?', [$code]));
    q('INSERT INTO rb_locations (business_id, name, code) VALUES (?, ?, ?)', [$bid, mb_substr(input('name'), 0, 120) ?: 'Νέο σημείο', $code]);
    redirect('t/review-booster/locations/' . db()->lastInsertId());
}
$locations = qall('SELECT l.*, (SELECT COUNT(*) FROM rb_ratings r WHERE r.location_id = l.id AND r.created_at >= CURDATE() - INTERVAL 29 DAY) AS n,
                          (SELECT AVG(stars) FROM rb_ratings r WHERE r.location_id = l.id AND r.created_at >= CURDATE() - INTERVAL 29 DAY) AS avg
                   FROM rb_locations l WHERE l.business_id = ? ORDER BY l.id', [$bid]);
module_page('locations', compact('locations', 'limit'), 'Σημεία & QR', 'locations');
