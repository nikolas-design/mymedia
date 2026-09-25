<?php
$biz = require_business();
$bid = (int) $biz['id'];

$subs = business_subscriptions($bid);
$teamCount = (int) qval('SELECT COUNT(*) FROM memberships WHERE business_id = ? AND active = 1', [$bid]);
$openRequests = qall(
    "SELECT r.*, t.name AS tool_name, t.icon, t.color, t.slug FROM tool_requests r JOIN tools t ON t.id = r.tool_id
     WHERE r.business_id = ? AND r.status IN ('new','setup') ORDER BY r.created_at DESC",
    [$bid]
);
$balance = (int) qval("SELECT COALESCE(SUM(total_cents),0) FROM invoices WHERE business_id = ? AND status = 'issued'", [$bid]);
$openTickets = (int) qval("SELECT COUNT(*) FROM tickets WHERE business_id = ? AND status <> 'closed'", [$bid]);

render('dashboard', compact('biz', 'subs', 'teamCount', 'openRequests', 'balance', 'openTickets'),
    ['title' => 'Αρχική', 'nav' => 'dashboard']);
