<?php
$wd = (int) date('N');
$groups = qall("SELECT g.*, (SELECT COUNT(*) FROM ls_enrollments e JOIN ls_students s ON s.id = e.student_id WHERE e.group_id = g.id AND s.active = 1) AS n,
                (SELECT COUNT(*) FROM ls_attendance a WHERE a.group_id = g.id AND a.day = CURDATE()) AS marked
                FROM ls_groups g WHERE g.business_id = ? AND g.active = 1 AND FIND_IN_SET(?, g.weekdays) ORDER BY g.start_time", [$bid, $wd]);
$stats = q1('SELECT (SELECT COUNT(*) FROM ls_students WHERE business_id = ? AND active = 1) AS students, (SELECT COUNT(*) FROM ls_groups WHERE business_id = ? AND active = 1) AS groups_n', [$bid, $bid]);
$owed = $canEdit ? (int) qval('SELECT COALESCE(SUM(amount_cents - paid_cents),0) FROM ls_charges WHERE business_id = ?', [$bid]) : 0;
module_page('today', compact('groups', 'stats', 'owed'), 'Σήμερα', 'today');
