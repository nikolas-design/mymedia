<?php
rb_ensure_location($bid, $business['name']);
$locations = qall('SELECT * FROM rb_locations WHERE business_id = ? AND active = 1 ORDER BY id', [$bid]);
if (is_post()) {
    $loc = null;
    foreach ($locations as $l) {
        if ((int) $l['id'] === input_int('location_id')) {
            $loc = $l;
        }
    }
    $email = mb_strtolower(input('email'));
    $name = mb_substr(input('name'), 0, 120);
    if (!$loc || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        flash('Γράψε έγκυρο email.', 'error');
        redirect('t/review-booster/send');
    }
    // Όχι δεύτερο αίτημα στον ίδιο πελάτη μέσα σε 30 ημέρες
    if (qval('SELECT 1 FROM rb_requests WHERE business_id = ? AND email = ? AND created_at > NOW() - INTERVAL 30 DAY', [$bid, $email])) {
        flash('Σε αυτό το email στάλθηκε ήδη αίτημα τις τελευταίες 30 ημέρες.', 'info');
        redirect('t/review-booster/send');
    }
    $sent = send_mail($email, 'Πώς ήταν η εμπειρία σας στο ' . $business['name'] . ';',
        'Γεια σας' . ($name ? ' ' . $name : '') . ",\n\nΕυχαριστούμε που μας επιλέξατε! Θα μας λέγατε σε 10 δευτερόλεπτα πώς ήταν η εμπειρία σας;\n\n"
        . rb_public_url($loc['code']) . "?s=email\n\nΜε εκτίμηση,\n" . $business['name']);
    q('INSERT INTO rb_requests (business_id, location_id, name, email, sent_by) VALUES (?, ?, ?, ?, ?)', [$bid, $loc['id'], $name ?: null, $email, $user['id']]);
    flash($sent ? 'Το αίτημα στάλθηκε στο ' . $email . '.' : 'Το email δεν έφυγε από τον server. Έλεγξε τις ρυθμίσεις email του hosting.', $sent ? 'ok' : 'error');
    redirect('t/review-booster/send');
}
$history = qall('SELECT r.*, l.name AS location, u.name AS by_name FROM rb_requests r JOIN rb_locations l ON l.id = r.location_id
                 LEFT JOIN users u ON u.id = r.sent_by WHERE r.business_id = ? ORDER BY r.id DESC LIMIT 20', [$bid]);
module_page('send', compact('locations', 'history'), 'Αποστολή αιτήματος', 'send');
