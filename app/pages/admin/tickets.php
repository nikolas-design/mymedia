<?php
require_admin();
$status = (string) ($_GET['status'] ?? 'open');
$where = match ($status) {
    'all' => '1=1',
    'answered' => "t.status = 'answered'",
    'closed' => "t.status = 'closed'",
    default => "t.status = 'open'",
};
$tickets = qall("SELECT t.*, b.name AS business_name, (SELECT COUNT(*) FROM ticket_messages m WHERE m.ticket_id = t.id) AS n
                 FROM tickets t JOIN businesses b ON b.id = t.business_id WHERE $where ORDER BY t.updated_at DESC LIMIT 200");
render('admin/tickets', compact('tickets', 'status'), ['title' => 'Υποστήριξη', 'nav' => 'admin-tickets', 'area' => 'admin']);
