<?php
$g = q1('SELECT * FROM ls_groups WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$g) {
    not_found();
}
$day = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_REQUEST['d'] ?? '')) ? $_REQUEST['d'] : date('Y-m-d');
$students = qall('SELECT s.* FROM ls_enrollments e JOIN ls_students s ON s.id = e.student_id WHERE e.group_id = ? AND s.active = 1 ORDER BY s.name', [$g['id']]);
if (is_post()) {
    $present = array_map('intval', (array) ($_POST['present'] ?? []));
    foreach ($students as $s) {
        q('INSERT INTO ls_attendance (group_id, student_id, day, present) VALUES (?, ?, ?, ?) ON DUPLICATE KEY UPDATE present = VALUES(present)',
            [$g['id'], $s['id'], $day, in_array((int) $s['id'], $present, true) ? 1 : 0]);
    }
    flash('Οι παρουσίες αποθηκεύτηκαν.');
    redirect('t/lessons/attendance/' . $g['id'] . '?d=' . $day);
}
$marks = [];
foreach (qall('SELECT student_id, present FROM ls_attendance WHERE group_id = ? AND day = ?', [$g['id'], $day]) as $a) {
    $marks[(int) $a['student_id']] = (int) $a['present'];
}
module_page('attendance', compact('g', 'day', 'students', 'marks'), 'Παρουσίες · ' . $g['name'], 'today');
