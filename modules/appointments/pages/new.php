<?php
$services = qall('SELECT * FROM ap_services WHERE business_id = ? AND active = 1 ORDER BY sort, name', [$bid]);
$staff = qall('SELECT * FROM ap_staff WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
if (is_post()) {
    $service = q1('SELECT * FROM ap_services WHERE id = ? AND business_id = ?', [input_int('service_id'), $bid]);
    $staffRow = q1('SELECT * FROM ap_staff WHERE id = ? AND business_id = ?', [input_int('staff_id'), $bid]);
    $day = input('day');
    $time = input('time');
    $name = mb_substr(input('name'), 0, 120);
    $phone = input('phone');
    if (!$service || !$staffRow || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $day) || !preg_match('/^\d{2}:\d{2}$/', $time) || $name === '' || strlen(preg_replace('/\D/', '', $phone)) < 8) {
        flash('Συμπλήρωσε υπηρεσία, συνεργάτη, ημέρα, ώρα, όνομα και τηλέφωνο.', 'error');
        redirect('t/appointments/new');
    }
    $email = filter_var(input('email'), FILTER_VALIDATE_EMAIL) ? input('email') : null;
    $cid = ap_customer($bid, $name, $phone, $email);
    $id = ap_book($settings, $service, (int) $staffRow['id'], $day, $time, $cid, 'manual', mb_substr(input('notes'), 0, 500) ?: null, 'confirmed');
    if (!$id) {
        flash('Η ώρα δεν είναι πια ελεύθερη. Διάλεξε άλλη.', 'error');
        redirect('t/appointments/new');
    }
    if (input('send_email') === '1') {
        ap_mail_customer($id, 'confirmed');
    }
    flash('Το ραντεβού καταχωρήθηκε.');
    redirect('t/appointments?d=' . $day);
}
$preDay = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['day'] ?? '')) ? $_GET['day'] : date('Y-m-d');
$links = [];
foreach (qall('SELECT staff_id, service_id FROM ap_staff_services x JOIN ap_staff s ON s.id = x.staff_id WHERE s.business_id = ?', [$bid]) as $r) {
    $links[(int) $r['service_id']][] = (int) $r['staff_id'];
}
module_page('new', compact('services', 'staff', 'preDay', 'links'), 'Νέο ραντεβού', 'new');
