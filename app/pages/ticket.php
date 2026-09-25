<?php
$biz = require_business();
$me = current_user();
$t = q1('SELECT * FROM tickets WHERE id = ? AND business_id = ?', [(int) $params[0], $biz['id']]);
if (!$t) {
    not_found();
}
if (is_post()) {
    $body = input('body');
    if ($body !== '') {
        q('INSERT INTO ticket_messages (ticket_id, user_id, body) VALUES (?, ?, ?)', [$t['id'], $me['id'], $body]);
        q("UPDATE tickets SET status = 'open', updated_at = NOW() WHERE id = ?", [$t['id']]);
        notify(null, 'Απάντηση πελάτη: ' . $t['subject'], 'admin/tickets/' . $t['id']);
    }
    redirect('support/' . $t['id']);
}
$messages = qall('SELECT m.*, u.name FROM ticket_messages m LEFT JOIN users u ON u.id = m.user_id WHERE m.ticket_id = ? ORDER BY m.id', [$t['id']]);
render('ticket', ['t' => $t, 'messages' => $messages, 'back' => 'support', 'staff' => false],
    ['title' => $t['subject'], 'nav' => 'support']);
