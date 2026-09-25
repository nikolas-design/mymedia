<?php
module_require_edit();
$isNew = $params[0] === 'new';
$r = $isNew ? null : q1('SELECT * FROM hb_rooms WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$r) {
    not_found();
}
if (is_post()) {
    $action = input('action');
    $d = fn(string $k) => preg_match('/^\d{4}-\d{2}-\d{2}$/', input($k)) ? input($k) : null;
    if ($r && $action === 'rate' && $d('date_from') && $d('date_to') && input('price') !== '') {
        q('INSERT INTO hb_rates (room_id, date_from, date_to, price_cents, label) VALUES (?, ?, ?, ?, ?)', [$r['id'], $d('date_from'), max($d('date_from'), $d('date_to')), parse_money(input('price')), mb_substr(input('label'), 0, 60) ?: null]);
        redirect('t/hotel-booking/rooms/' . $r['id']);
    }
    if ($r && $action === 'block' && $d('date_from')) {
        q('INSERT INTO hb_blocks (room_id, date_from, date_to, units, reason) VALUES (?, ?, ?, ?, ?)', [$r['id'], $d('date_from'), max($d('date_from'), $d('date_to') ?? $d('date_from')), max(1, min((int) $r['units'], input_int('units') ?: 1)), mb_substr(input('reason'), 0, 120) ?: null]);
        redirect('t/hotel-booking/rooms/' . $r['id']);
    }
    if ($r && $action === 'del_rate') {
        q('DELETE FROM hb_rates WHERE id = ? AND room_id = ?', [input_int('id'), $r['id']]);
        redirect('t/hotel-booking/rooms/' . $r['id']);
    }
    if ($r && $action === 'del_block') {
        q('DELETE FROM hb_blocks WHERE id = ? AND room_id = ?', [input_int('id'), $r['id']]);
        redirect('t/hotel-booking/rooms/' . $r['id']);
    }
    if (input('name') === '' || input('price') === '') {
        flash('Συμπλήρωσε όνομα και τιμή.', 'error');
        redirect('t/hotel-booking/rooms/' . ($isNew ? 'new' : $r['id']));
    }
    try {
        $photo = store_image('photo', 'hotel-booking/' . $bid, 1600);
    } catch (RuntimeException $e) {
        flash($e->getMessage(), 'error');
        redirect('t/hotel-booking/rooms/' . ($isNew ? 'new' : $r['id']));
    }
    $data = [mb_substr(input('name'), 0, 120), mb_substr(input('description'), 0, 500) ?: null, max(1, min(20, input_int('capacity'))), max(1, min(200, input_int('units'))), parse_money(input('price'))];
    if ($isNew) {
        q('INSERT INTO hb_rooms (name, description, capacity, units, base_price_cents, photo, business_id) VALUES (?, ?, ?, ?, ?, ?, ?)', [...$data, $photo, $bid]);
        redirect('t/hotel-booking/rooms/' . db()->lastInsertId());
    }
    if ($photo) {
        delete_upload($r['photo']);
    }
    q('UPDATE hb_rooms SET name = ?, description = ?, capacity = ?, units = ?, base_price_cents = ?, photo = COALESCE(?, photo), active = ? WHERE id = ?', [...$data, $photo, input('active') === '1' ? 1 : 0, $r['id']]);
    flash('Αποθηκεύτηκε.');
    redirect('t/hotel-booking/rooms/' . $r['id']);
}
$rates = $r ? qall('SELECT * FROM hb_rates WHERE room_id = ? AND date_to >= CURDATE() ORDER BY date_from', [$r['id']]) : [];
$blocks = $r ? qall('SELECT * FROM hb_blocks WHERE room_id = ? AND date_to >= CURDATE() ORDER BY date_from', [$r['id']]) : [];
module_page('room', compact('r', 'isNew', 'rates', 'blocks'), $isNew ? 'Νέο δωμάτιο' : $r['name'], 'rooms');
