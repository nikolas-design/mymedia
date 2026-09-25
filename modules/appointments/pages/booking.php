<?php
$b = q1('SELECT b.*, c.name AS customer, c.phone, c.email, s.name AS staff FROM ap_bookings b JOIN ap_customers c ON c.id = b.customer_id
         JOIN ap_staff s ON s.id = b.staff_id WHERE b.id = ? AND b.business_id = ?', [(int) $params[0], $bid]);
if (!$b) {
    not_found();
}
if (is_post()) {
    $do = input('do');
    $map = ['confirm' => 'confirmed', 'done' => 'done', 'noshow' => 'noshow', 'cancel' => 'cancelled'];
    if (isset($map[$do])) {
        q('UPDATE ap_bookings SET status = ? WHERE id = ?', [$map[$do], $b['id']]);
        if (in_array($do, ['confirm', 'cancel'], true) && input('notify') === '1') {
            ap_mail_customer((int) $b['id'], $map[$do] === 'confirmed' ? 'confirmed' : 'cancelled');
        }
        flash('Ενημερώθηκε: ' . AP_STATUS[$map[$do]][0] . '.');
    } elseif ($do === 'move') {
        $day = input('day');
        $time = input('time');
        $dur = (int) ((strtotime($b['ends_at']) - strtotime($b['starts_at'])) / 60);
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) && in_array($time, ap_free_slots($settings, (int) $b['staff_id'], $day, $dur, false, (int) $b['id']), true)) {
            $start = "$day $time:00";
            q('UPDATE ap_bookings SET starts_at = ?, ends_at = ?, reminded_at = NULL WHERE id = ?', [$start, date('Y-m-d H:i:s', strtotime($start) + $dur * 60), $b['id']]);
            if (input('notify') === '1') {
                ap_mail_customer((int) $b['id'], 'moved');
            }
            flash('Το ραντεβού μεταφέρθηκε.');
        } else {
            flash('Η ώρα δεν είναι ελεύθερη.', 'error');
        }
    } elseif ($do === 'notes') {
        q('UPDATE ap_bookings SET notes = ? WHERE id = ?', [mb_substr(input('notes'), 0, 500) ?: null, $b['id']]);
        flash('Αποθηκεύτηκε.');
    }
    redirect('t/appointments/bookings/' . $b['id']);
}
$history = (int) qval("SELECT COUNT(*) FROM ap_bookings WHERE customer_id = ? AND status = 'done'", [$b['customer_id']]);
$noshows = (int) qval("SELECT COUNT(*) FROM ap_bookings WHERE customer_id = ? AND status = 'noshow'", [$b['customer_id']]);
module_page('booking', compact('b', 'history', 'noshows'), 'Ραντεβού', 'calendar');
