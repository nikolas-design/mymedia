<?php
module_require_edit();
$limit = $pro ? 100 : 15;
if (is_post()) {
    $action = input('action');
    $count = (int) qval('SELECT COUNT(*) FROM sh_people WHERE business_id = ? AND active = 1', [$bid]);
    if ($action === 'import') {
        // Από την ομάδα της πύλης
        $n = 0;
        foreach (qall('SELECT u.id, u.name, u.email, m.job_title FROM memberships m JOIN users u ON u.id = m.user_id WHERE m.business_id = ? AND m.active = 1', [$bid]) as $u) {
            if ($count + $n >= $limit) {
                break;
            }
            if (!qval('SELECT 1 FROM sh_people WHERE business_id = ? AND (user_id = ? OR email = ?)', [$bid, $u['id'], $u['email']])) {
                q('INSERT INTO sh_people (business_id, user_id, name, email, position) VALUES (?, ?, ?, ?, ?)', [$bid, $u['id'], $u['name'], $u['email'], $u['job_title']]);
                $n++;
            }
        }
        flash($n ? "Προστέθηκαν $n άτομα από την ομάδα." : 'Όλη η ομάδα υπάρχει ήδη.', $n ? 'ok' : 'info');
    } elseif ($action === 'save') {
        $id = input_int('id');
        $email = input('email');
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('Το email δεν είναι έγκυρο.', 'error');
            redirect('t/shifts/people');
        }
        $data = [mb_substr(input('name'), 0, 120), mb_substr(input('position'), 0, 60) ?: null, input('phone') ?: null, $email ?: null,
            input('weekly_hours') === '' ? null : (float) str_replace(',', '.', input('weekly_hours'))];
        if ($data[0] === '') {
            flash('Γράψε όνομα.', 'error');
        } elseif ($id) {
            q('UPDATE sh_people SET name = ?, position = ?, phone = ?, email = ?, weekly_hours = ?, active = ? WHERE id = ? AND business_id = ?',
                [...$data, input('active') === '1' ? 1 : 0, $id, $bid]);
            flash('Αποθηκεύτηκε.');
        } elseif ($count >= $limit) {
            flash("Το πλάνο σου περιλαμβάνει έως $limit άτομα.", 'error');
        } else {
            q('INSERT INTO sh_people (name, position, phone, email, weekly_hours, business_id) VALUES (?, ?, ?, ?, ?, ?)', [...$data, $bid]);
            flash('Προστέθηκε.');
        }
    }
    redirect('t/shifts/people');
}
$people = qall('SELECT * FROM sh_people WHERE business_id = ? ORDER BY active DESC, name', [$bid]);
module_page('people', compact('people', 'limit'), 'Προσωπικό', 'people');
