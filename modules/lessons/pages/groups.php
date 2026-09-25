<?php
$groups = qall('SELECT g.*, (SELECT COUNT(*) FROM ls_enrollments e JOIN ls_students s ON s.id = e.student_id WHERE e.group_id = g.id AND s.active = 1) AS n
                FROM ls_groups g WHERE g.business_id = ? ORDER BY g.active DESC, g.name', [$bid]);
module_page('groups', compact('groups'), 'Τμήματα', 'groups');
