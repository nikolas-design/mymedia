<?php
module_require_edit();
$staff = qall('SELECT s.*, (SELECT COUNT(*) FROM ap_hours h WHERE h.staff_id = s.id) AS hours FROM ap_staff s WHERE s.business_id = ? ORDER BY s.active DESC, s.name', [$bid]);
module_page('staff', compact('staff'), 'Συνεργάτες', 'staff');
