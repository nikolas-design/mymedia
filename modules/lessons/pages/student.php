<?php
$isNew = $params[0] === 'new';
$s = $isNew ? null : q1('SELECT * FROM ls_students WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$s) {
    not_found();
}
if (is_post()) {
    module_require_edit();
    $action = input('action');
    if ($s && $action === 'pay') {
        // Πληρωμή: καλύπτει τις παλαιότερες οφειλές πρώτα
        $amount = parse_money(input('amount'));
        foreach (qall('SELECT * FROM ls_charges WHERE student_id = ? AND paid_cents < amount_cents ORDER BY month, id', [$s['id']]) as $c) {
            if ($amount <= 0) {
                break;
            }
            $take = min($amount, (int) $c['amount_cents'] - (int) $c['paid_cents']);
            q('UPDATE ls_charges SET paid_cents = paid_cents + ?, paid_at = CURDATE() WHERE id = ?', [$take, $c['id']]);
            $amount -= $take;
        }
        flash($amount > 0 ? 'Η πληρωμή καταχωρήθηκε. Περίσσεψαν ' . money($amount) . ' (δεν υπάρχουν άλλες οφειλές).' : 'Η πληρωμή καταχωρήθηκε.');
        redirect('t/lessons/students/' . $s['id']);
    }
    if ($s && $action === 'charge' && parse_money(input('amount')) > 0) {
        q('INSERT INTO ls_charges (business_id, student_id, month, description, amount_cents) VALUES (?, ?, ?, ?, ?)',
            [$bid, $s['id'], date('Y-m'), mb_substr(input('description'), 0, 190) ?: 'Χρέωση', parse_money(input('amount'))]);
        redirect('t/lessons/students/' . $s['id']);
    }
    $email = input('email');
    $data = [mb_substr(input('name'), 0, 120), input('parent_name') ?: null, input('phone') ?: null, filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : null, mb_substr(input('notes'), 0, 500) ?: null];
    if ($data[0] === '') {
        flash('Γράψε όνομα.', 'error');
        redirect('t/lessons/students/' . ($isNew ? 'new' : $s['id']));
    }
    if ($isNew) {
        q('INSERT INTO ls_students (name, parent_name, phone, email, notes, business_id, token) VALUES (?, ?, ?, ?, ?, ?, ?)', [...$data, $bid, bin2hex(random_bytes(12))]);
        $sid = (int) db()->lastInsertId();
        if (input_int('group_id') && qval('SELECT 1 FROM ls_groups WHERE id = ? AND business_id = ?', [input_int('group_id'), $bid])) {
            q('INSERT INTO ls_enrollments (group_id, student_id, since) VALUES (?, ?, CURDATE())', [input_int('group_id'), $sid]);
        }
        flash('Ο μαθητής προστέθηκε.');
        redirect('t/lessons/students/' . $sid);
    }
    q('UPDATE ls_students SET name = ?, parent_name = ?, phone = ?, email = ?, notes = ?, active = ? WHERE id = ?', [...$data, input('active') === '1' ? 1 : 0, $s['id']]);
    flash('Αποθηκεύτηκε.');
    redirect('t/lessons/students/' . $s['id']);
}
$groups = qall('SELECT id, name FROM ls_groups WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
$mine = $s ? qall('SELECT g.*, (SELECT ROUND(AVG(present)*100) FROM ls_attendance a WHERE a.group_id = g.id AND a.student_id = ?) AS att FROM ls_enrollments e JOIN ls_groups g ON g.id = e.group_id WHERE e.student_id = ?', [$s['id'], $s['id']]) : [];
$charges = $s && $canEdit ? qall('SELECT * FROM ls_charges WHERE student_id = ? ORDER BY month DESC, id DESC LIMIT 36', [$s['id']]) : [];
module_page('student', compact('s', 'isNew', 'groups', 'mine', 'charges'), $isNew ? 'Νέος μαθητής' : $s['name'], 'students');
