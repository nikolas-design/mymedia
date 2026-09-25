<?php
$isNew = $params[0] === 'new';
$b = $isNew ? null : q1('SELECT b.*, r.name AS room FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id WHERE b.id = ? AND b.business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$b) {
    not_found();
}
$rooms = qall('SELECT * FROM hb_rooms WHERE business_id = ? AND active = 1 ORDER BY sort, id', [$bid]);
if (is_post()) {
    if (!$isNew && ($do = input('do')) !== '') {
        $map = ['confirm' => 'confirmed', 'checkin' => 'checked_in', 'complete' => 'completed', 'cancel' => 'cancelled'];
        if (isset($map[$do])) {
            q('UPDATE hb_bookings SET status = ? WHERE id = ?', [$map[$do], $b['id']]);
            if (in_array($do, ['confirm', 'cancel'], true)) {
                hb_mail_guest((int) $b['id'], $map[$do]);
            }
            flash(HB_STATUS[$map[$do]][0] . '.');
        } elseif ($do === 'paid') {
            q('UPDATE hb_bookings SET paid_cents = ?, notes = ? WHERE id = ?', [parse_money(input('paid')), mb_substr(input('notes'), 0, 500) ?: null, $b['id']]);
            flash('Αποθηκεύτηκε.');
        }
        redirect('t/hotel-booking/bookings/' . $b['id']);
    }
    // Νέα κράτηση από την ομάδα (τηλέφωνο, email, άλλη πλατφόρμα)
    $room = null;
    foreach ($rooms as $r) {
        if ((int) $r['id'] === input_int('room_id')) {
            $room = $r;
        }
    }
    $in = input('checkin');
    $out = input('checkout');
    if (!$room || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $in) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $out) || $out <= $in || input('name') === '') {
        flash('Έλεγξε δωμάτιο, ημερομηνίες και όνομα.', 'error');
        redirect('t/hotel-booking/bookings/new');
    }
    if (hb_free_units($room, $in, $out) < 1) {
        flash('Δεν υπάρχει διαθεσιμότητα για αυτές τις ημερομηνίες.', 'error');
        redirect('t/hotel-booking/bookings/new');
    }
    $total = input('total') !== '' ? parse_money(input('total')) : hb_price($room, $in, $out);
    q("INSERT INTO hb_bookings (business_id, room_id, token, checkin, checkout, guests, name, email, phone, notes, total_cents, status, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'confirmed', ?)", [
        $bid, $room['id'], bin2hex(random_bytes(16)), $in, $out, max(1, input_int('guests')), mb_substr(input('name'), 0, 120),
        mb_substr(input('email'), 0, 190), mb_substr(input('phone'), 0, 40), mb_substr(input('notes'), 0, 500) ?: null, $total, input('source') === 'other' ? 'other' : 'phone',
    ]);
    flash('Η κράτηση καταχωρήθηκε.');
    redirect('t/hotel-booking/bookings/' . db()->lastInsertId());
}
module_page('booking', compact('b', 'isNew', 'rooms', 'settings'), $isNew ? 'Νέα κράτηση' : $b['name'], 'bookings');
