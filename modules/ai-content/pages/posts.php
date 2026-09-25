<?php
$status = array_key_exists($_GET['status'] ?? '', AI_STATUS) ? $_GET['status'] : '';
$args = [$bid];
$where = '';
if ($status) {
    $where = ' AND status = ?';
    $args[] = $status;
} else {
    $where = " AND (for_date IS NULL OR for_date >= CURDATE() - INTERVAL 7 DAY OR status <> 'posted')";
}
$posts = qall("SELECT * FROM ai_posts WHERE business_id = ?$where ORDER BY for_date IS NULL, for_date, id LIMIT 200", $args);
$profileEmpty = !$profile['description'];
module_page('posts', compact('posts', 'status', 'profileEmpty'), 'Πλάνο posts', 'posts');
