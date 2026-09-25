<?php
$isNew = $params[0] === 'new';
$g = $isNew ? null : q1('SELECT * FROM ls_groups WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$g) {
    not_found();
}
if (is_post()) {
    module_require_edit();
    $action = input('action');
    if ($g && $action === 'enroll') {
        $s = q1('SELECT id FROM ls_students WHERE id = ? AND business_id = ?', [input_int('student_id'), $bid]);
        if ($s) {
            q('INSERT IGNORE INTO ls_enrollments (group_id, student_id, since) VALUES (?, ?, CURDATE())', [$g['id'], $s['id']]);
        }
        redirect('t/lessons/groups/' . $g['id']);
    }
    if ($g && $action === 'unenroll') {
        q('DELETE FROM ls_enrollments WHERE group_id = ? AND student_id = ?', [$g['id'], input_int('student_id')]);
        redirect('t/lessons/groups/' . $g['id']);
    }
    if ($g && $action === 'delete') {
        q('DELETE FROM ls_groups WHERE id = ?', [$g['id']]);
        redirect('t/lessons/groups');
    }
    $days = implode(',', array_filter(array_map('intval', (array) ($_POST['weekdays'] ?? [])), fn($d) => $d >= 1 && $d <= 7));
    $t = fn(string $k) => preg_match('/^\d{2}:\d{2}$/', input($k)) ? input($k) : null;
    $data = [mb_substr(input('name'), 0, 120) ?: 'Τμήμα', input('teacher') ?: null, input('room') ?: null, $days ?: null, $t('start_time'), $t('end_time'),
        input_int('capacity') ?: null, parse_money(input('fee') ?: '0')];
    if ($isNew) {
        q('INSERT INTO ls_groups (name, teacher, room, weekdays, start_time, end_time, capacity, monthly_fee_cents, business_id) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)', [...$data, $bid]);
        redirect('t/lessons/groups/' . db()->lastInsertId());
    }
    q('UPDATE ls_groups SET name = ?, teacher = ?, room = ?, weekdays = ?, start_time = ?, end_time = ?, capacity = ?, monthly_fee_cents = ?, active = ? WHERE id = ?', [...$data, input('active') === '1' ? 1 : 0, $g['id']]);
    flash('Αποθηκεύτηκε.');
    redirect('t/lessons/groups/' . $g['id']);
}
$students = $g ? qall('SELECT s.*, (SELECT ROUND(AVG(a.present) * 100) FROM ls_attendance a WHERE a.student_id = s.id AND a.group_id = ?) AS att
                      FROM ls_enrollments e JOIN ls_students s ON s.id = e.student_id WHERE e.group_id = ? ORDER BY s.name', [$g['id'], $g['id']]) : [];
$others = $g ? qall('SELECT id, name FROM ls_students WHERE business_id = ? AND active = 1 AND id NOT IN (SELECT student_id FROM ls_enrollments WHERE group_id = ?) ORDER BY name', [$bid, $g['id']]) : [];
module_page('group', compact('g', 'isNew', 'students', 'others'), $isNew ? 'Νέο τμήμα' : $g['name'], 'groups');
