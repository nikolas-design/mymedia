<?php
$me = sh_me($bid, $user);
if (is_post()) {
    if (input('action') === 'decide') {
        module_require_edit();
        $r = q1("SELECT r.*, p.name FROM sh_requests r JOIN sh_people p ON p.id = r.person_id WHERE r.id = ? AND r.business_id = ? AND r.status = 'new'", [input_int('id'), $bid]);
        if ($r) {
            $status = input('status') === 'approved' ? 'approved' : 'rejected';
            q('UPDATE sh_requests SET status = ?, decided_by = ? WHERE id = ?', [$status, $user['id'], $r['id']]);
            flash(($status === 'approved' ? 'Εγκρίθηκε: ' : 'Απορρίφθηκε: ') . SH_KIND[$r['kind']] . ' για ' . $r['name'] . '.');
        }
        redirect('t/shifts/requests');
    }
    // Νέο αίτημα: ο ίδιος για τον εαυτό του, ή ο υπεύθυνος για οποιονδήποτε
    $personId = $canEdit && input_int('person_id') ? input_int('person_id') : (int) ($me['id'] ?? 0);
    $person = q1('SELECT * FROM sh_people WHERE id = ? AND business_id = ?', [$personId, $bid]);
    $from = input('day_from');
    $to = input('day_to') ?: $from;
    if (!$person || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $from) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $to) || $to < $from) {
        flash('Έλεγξε τις ημερομηνίες.', 'error');
        redirect('t/shifts/requests');
    }
    $kind = array_key_exists(input('kind'), SH_KIND) ? input('kind') : 'off';
    q('INSERT INTO sh_requests (business_id, person_id, kind, day_from, day_to, note, status, decided_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
        [$bid, $person['id'], $kind, $from, $to, mb_substr(input('note'), 0, 500) ?: null, $canEdit ? 'approved' : 'new', $canEdit ? $user['id'] : null]);
    if (!$canEdit) {
        notify($bid, 'Αίτημα βάρδιας: ' . $person['name'] . ' · ' . SH_KIND[$kind] . ' ' . date_gr($from, false), 't/shifts/requests');
    }
    flash($canEdit ? 'Καταχωρήθηκε.' : 'Το αίτημα στάλθηκε στον υπεύθυνο.');
    redirect('t/shifts/requests');
}
$where = $canEdit ? '' : ' AND r.person_id = ' . (int) ($me['id'] ?? 0);
$requests = qall("SELECT r.*, p.name FROM sh_requests r JOIN sh_people p ON p.id = r.person_id WHERE r.business_id = ?$where
                  ORDER BY r.status = 'new' DESC, r.day_from DESC LIMIT 100", [$bid]);
$people = $canEdit ? qall('SELECT id, name FROM sh_people WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]) : [];
module_page('requests', compact('requests', 'people', 'me'), 'Αιτήματα', 'requests');
