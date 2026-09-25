<?php
$q = trim((string) ($_GET['q'] ?? ''));
$args = [$bid];
$where = '';
if ($q !== '') {
    $where = ' AND (s.name LIKE ? OR s.parent_name LIKE ? OR s.phone LIKE ?)';
    array_push($args, "%$q%", "%$q%", "%$q%");
}
$students = qall("SELECT s.*, (SELECT GROUP_CONCAT(g.name SEPARATOR ', ') FROM ls_enrollments e JOIN ls_groups g ON g.id = e.group_id WHERE e.student_id = s.id) AS groups_list,
                  (SELECT COALESCE(SUM(amount_cents - paid_cents),0) FROM ls_charges c WHERE c.student_id = s.id) AS balance
                  FROM ls_students s WHERE s.business_id = ?$where ORDER BY s.active DESC, s.name LIMIT 500", $args);
module_page('students', compact('students', 'q'), 'Μαθητές', 'students');
