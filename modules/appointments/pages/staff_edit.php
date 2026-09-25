<?php
module_require_edit();
$isNew = $params[0] === 'new';
$s = $isNew ? null : q1('SELECT * FROM ap_staff WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$s) {
    not_found();
}
$services = qall('SELECT * FROM ap_services WHERE business_id = ? ORDER BY sort, name', [$bid]);
if (is_post()) {
    $action = input('action');
    if ($action === 'timeoff' && $s) {
        if (preg_match('/^\d{4}-\d{2}-\d{2}$/', input('day_from'))) {
            q('INSERT INTO ap_timeoff (staff_id, day_from, day_to, reason) VALUES (?, ?, ?, ?)',
                [$s['id'], input('day_from'), max(input('day_from'), input('day_to') ?: input('day_from')), mb_substr(input('reason'), 0, 120) ?: null]);
            flash('Η άδεια καταχωρήθηκε. Ραντεβού που υπάρχουν ήδη εκείνες τις μέρες δεν ακυρώνονται αυτόματα.');
        }
        redirect('t/appointments/staff/' . $s['id']);
    }
    if ($action === 'timeoff_delete' && $s) {
        q('DELETE FROM ap_timeoff WHERE id = ? AND staff_id = ?', [input_int('id'), $s['id']]);
        redirect('t/appointments/staff/' . $s['id']);
    }
    if ($action === 'delete' && $s) {
        q('DELETE FROM ap_staff WHERE id = ?', [$s['id']]);
        flash('Ο συνεργάτης διαγράφηκε μαζί με τα ραντεβού του.', 'info');
        redirect('t/appointments/staff');
    }
    $name = mb_substr(input('name'), 0, 120);
    if ($name === '') {
        flash('Γράψε όνομα.', 'error');
        redirect('t/appointments/staff/' . ($isNew ? 'new' : $s['id']));
    }
    $color = preg_match('/^#[0-9a-fA-F]{6}$/', input('color')) ? input('color') : '#793de7';
    if ($isNew && input('color') === '#d64b7c') {
        // Διαφορετικό χρώμα για κάθε νέο συνεργάτη, για να ξεχωρίζουν στο ημερολόγιο
        $palette = ['#d64b7c', '#793de7', '#2f8fd6', '#1d9e75', '#e79b23', '#4c5fd8', '#c2410c'];
        $color = $palette[(int) qval('SELECT COUNT(*) FROM ap_staff WHERE business_id = ?', [$bid]) % count($palette)];
    }
    if ($isNew) {
        q('INSERT INTO ap_staff (business_id, name, color) VALUES (?, ?, ?)', [$bid, $name, $color]);
        $sid = (int) db()->lastInsertId();
    } else {
        $sid = (int) $s['id'];
        q('UPDATE ap_staff SET name = ?, color = ?, active = ? WHERE id = ?', [$name, $color, input('active') === '1' ? 1 : 0, $sid]);
    }
    // Υπηρεσίες
    q('DELETE FROM ap_staff_services WHERE staff_id = ?', [$sid]);
    $valid = array_map('intval', array_column($services, 'id'));
    foreach ((array) ($_POST['services'] ?? []) as $svc) {
        if (in_array((int) $svc, $valid, true)) {
            q('INSERT INTO ap_staff_services (staff_id, service_id) VALUES (?, ?)', [$sid, (int) $svc]);
        }
    }
    // Ωράριο: έως 2 διαστήματα ανά ημέρα
    q('DELETE FROM ap_hours WHERE staff_id = ?', [$sid]);
    foreach (range(1, 7) as $d) {
        foreach ([1, 2] as $k) {
            $from = (string) ($_POST['h'][$d][$k]['from'] ?? '');
            $to = (string) ($_POST['h'][$d][$k]['to'] ?? '');
            if (preg_match('/^\d{2}:\d{2}$/', $from) && preg_match('/^\d{2}:\d{2}$/', $to) && $to > $from) {
                q('INSERT INTO ap_hours (staff_id, weekday, start_time, end_time) VALUES (?, ?, ?, ?)', [$sid, $d, $from, $to]);
            }
        }
    }
    flash('Αποθηκεύτηκε.');
    redirect('t/appointments/staff/' . $sid);
}
$hours = [];
$mine = [];
$timeoff = [];
if ($s) {
    foreach (qall('SELECT * FROM ap_hours WHERE staff_id = ? ORDER BY weekday, start_time', [$s['id']]) as $h) {
        $hours[(int) $h['weekday']][] = $h;
    }
    $mine = array_map('intval', array_column(qall('SELECT service_id FROM ap_staff_services WHERE staff_id = ?', [$s['id']]), 'service_id'));
    $timeoff = qall('SELECT * FROM ap_timeoff WHERE staff_id = ? AND day_to >= CURDATE() ORDER BY day_from', [$s['id']]);
} else {
    // Προτεινόμενο ωράριο για νέο συνεργάτη
    foreach ([2, 3, 4, 5] as $d) {
        $hours[$d] = [['start_time' => '09:00', 'end_time' => '14:00'], ['start_time' => '17:00', 'end_time' => '21:00']];
    }
    $hours[6] = [['start_time' => '09:00', 'end_time' => '15:00']];
    $mine = array_map('intval', array_column($services, 'id'));
}
module_page('staff_edit', compact('s', 'isNew', 'services', 'hours', 'mine', 'timeoff'), $isNew ? 'Νέος συνεργάτης' : $s['name'], 'staff');
