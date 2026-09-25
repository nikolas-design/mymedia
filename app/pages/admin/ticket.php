<?php
$me = require_admin();
$t = q1('SELECT t.*, b.name AS business_name FROM tickets t JOIN businesses b ON b.id = t.business_id WHERE t.id = ?', [(int) $params[0]]);
if (!$t) {
    not_found();
}
if (is_post()) {
    if (input('do') === 'close') {
        q("UPDATE tickets SET status = 'closed', updated_at = NOW() WHERE id = ?", [$t['id']]);
        flash('Το θέμα έκλεισε.', 'info');
    } elseif (input('body') !== '') {
        q('INSERT INTO ticket_messages (ticket_id, user_id, from_staff, body) VALUES (?, ?, 1, ?)', [$t['id'], $me['id'], input('body')]);
        q("UPDATE tickets SET status = 'answered', updated_at = NOW() WHERE id = ?", [$t['id']]);
        notify((int) $t['business_id'], 'Νέα απάντηση: ' . $t['subject'], 'support/' . $t['id']);
        // Email στον χρήστη που άνοιξε το θέμα
        if ($t['user_id'] && ($u = q1('SELECT email, name FROM users WHERE id = ?', [$t['user_id']]))) {
            send_mail($u['email'], 'Απάντηση: ' . $t['subject'],
                "Γεια σου {$u['name']},\n\nΑπαντήσαμε στο αίτημά σου:\n\n" . input('body') . "\n\nΔες ολόκληρη τη συζήτηση: " . full_url('support/' . $t['id']));
        }
        flash('Η απάντηση στάλθηκε.');
    }
    redirect('admin/tickets/' . $t['id']);
}
$messages = qall('SELECT m.*, u.name FROM ticket_messages m LEFT JOIN users u ON u.id = m.user_id WHERE m.ticket_id = ? ORDER BY m.id', [$t['id']]);
render('ticket', ['t' => $t, 'messages' => $messages, 'back' => 'admin/tickets', 'staff' => true],
    ['title' => $t['subject'], 'nav' => 'admin-tickets', 'area' => 'admin']);
