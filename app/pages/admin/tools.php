<?php
require_admin();
$tools = qall("SELECT t.*,
                 (SELECT COUNT(*) FROM subscriptions s WHERE s.tool_id = t.id AND s.status = 'active') AS subs,
                 (SELECT COUNT(*) FROM plans p WHERE p.tool_id = t.id AND p.active = 1) AS plans
               FROM tools t ORDER BY t.sort");
render('admin/tools', compact('tools'), ['title' => 'Εργαλεία & τιμές', 'nav' => 'admin-tools', 'area' => 'admin']);
