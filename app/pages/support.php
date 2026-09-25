<?php
$biz = require_business();
$bid = (int) $biz['id'];
$me = current_user();

if (is_post()) {
    $subject = input('subject');
    $body = input('body');
    if (mb_strlen($subject) < 3 || mb_strlen($body) < 3) {
        flash('Συμπλήρωσε θέμα και μήνυμα.', 'error');
        redirect('support');
    }
    q('INSERT INTO tickets (business_id, user_id, subject) VALUES (?, ?, ?)', [$bid, $me['id'], mb_substr($subject, 0, 160)]);
    $tid = (int) db()->lastInsertId();
    q('INSERT INTO ticket_messages (ticket_id, user_id, body) VALUES (?, ?, ?)', [$tid, $me['id'], $body]);
    notify(null, 'Νέο αίτημα υποστήριξης: ' . $biz['name'], 'admin/tickets/' . $tid);
    flash('Το αίτημα στάλθηκε. Θα σου απαντήσουμε σύντομα.');
    redirect('support/' . $tid);
}

$tickets = qall('SELECT t.*, (SELECT COUNT(*) FROM ticket_messages m WHERE m.ticket_id = t.id) AS n
                 FROM tickets t WHERE t.business_id = ? ORDER BY t.status = \'closed\', t.updated_at DESC', [$bid]);
render('support', compact('tickets'), ['title' => 'Υποστήριξη', 'nav' => 'support']);
