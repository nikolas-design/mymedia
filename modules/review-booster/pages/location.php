<?php
$l = q1('SELECT * FROM rb_locations WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$l) {
    not_found();
}
if (is_post()) {
    module_require_edit();
    if (input('action') === 'delete') {
        if ((int) qval('SELECT COUNT(*) FROM rb_locations WHERE business_id = ?', [$bid]) <= 1) {
            flash('Χρειάζεται τουλάχιστον ένα σημείο.', 'error');
            redirect('t/review-booster/locations/' . $l['id']);
        }
        q('DELETE FROM rb_locations WHERE id = ?', [$l['id']]);
        flash('Το σημείο διαγράφηκε.', 'info');
        redirect('t/review-booster/locations');
    }
    $g = trim(input('google_url'));
    if ($g !== '' && !preg_match('#^https?://#i', $g)) {
        $g = 'https://' . $g;
    }
    if ($g !== '' && !filter_var($g, FILTER_VALIDATE_URL)) {
        flash('Ο σύνδεσμος Google δεν είναι έγκυρος.', 'error');
        redirect('t/review-booster/locations/' . $l['id']);
    }
    q('UPDATE rb_locations SET name = ?, google_url = ?, threshold = ?, question = ?, thanks = ?, color = ?, active = ? WHERE id = ?', [
        mb_substr(input('name'), 0, 120) ?: $l['name'], $g ?: null, in_array(input_int('threshold'), [4, 5], true) ? input_int('threshold') : 4,
        mb_substr(input('question'), 0, 190) ?: null, mb_substr(input('thanks'), 0, 255) ?: null,
        preg_match('/^#[0-9a-fA-F]{6}$/', input('color')) ? input('color') : '#793de7', input('active') === '1' ? 1 : 0, $l['id'],
    ]);
    flash('Αποθηκεύτηκε.');
    redirect('t/review-booster/locations/' . $l['id']);
}
$s = rb_summary($bid, date('Y-m-d', strtotime('-29 days')), null, (int) $l['id']);
module_page('location', compact('l', 's'), $l['name'], 'locations');
