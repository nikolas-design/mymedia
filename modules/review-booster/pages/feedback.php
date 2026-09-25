<?php
$status = ($_GET['status'] ?? 'new') === 'resolved' ? 'resolved' : (($_GET['status'] ?? '') === 'all' ? 'all' : 'new');
$where = $status === 'all' ? '' : ' AND f.status = ' . db()->quote($status);
$items = qall("SELECT f.*, l.name AS location FROM rb_feedback f JOIN rb_locations l ON l.id = f.location_id
               WHERE f.business_id = ?$where ORDER BY f.id DESC LIMIT 200", [$bid]);
module_page('feedback', compact('items', 'status'), 'Ιδιωτικά σχόλια', 'feedback');
