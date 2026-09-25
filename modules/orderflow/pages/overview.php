<?php
$suppliers = qall("SELECT s.*, (SELECT COUNT(*) FROM of_products p WHERE p.supplier_id = s.id AND p.active = 1) AS products,
                   (SELECT MAX(created_at) FROM of_orders o WHERE o.supplier_id = s.id AND o.status IN ('sent','received')) AS last_order
                   FROM of_suppliers s WHERE s.business_id = ? AND s.active = 1 ORDER BY s.name", [$bid]);
$drafts = qall("SELECT o.*, s.name AS supplier FROM of_orders o JOIN of_suppliers s ON s.id = o.supplier_id
                WHERE o.business_id = ? AND o.status = 'draft' ORDER BY o.id DESC", [$bid]);
$pending = qall("SELECT o.*, s.name AS supplier FROM of_orders o JOIN of_suppliers s ON s.id = o.supplier_id
                 WHERE o.business_id = ? AND o.status = 'sent' ORDER BY o.delivery_date IS NULL, o.delivery_date, o.id", [$bid]);
$month = (int) qval("SELECT COALESCE(SUM(total_cents),0) FROM of_orders WHERE business_id = ? AND status IN ('sent','received') AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", [$bid]);
$prevMonth = (int) qval("SELECT COALESCE(SUM(total_cents),0) FROM of_orders WHERE business_id = ? AND status IN ('sent','received')
                         AND created_at >= DATE_FORMAT(CURDATE() - INTERVAL 1 MONTH, '%Y-%m-01') AND created_at < DATE_FORMAT(CURDATE(), '%Y-%m-01')", [$bid]);
module_page('overview', compact('suppliers', 'drafts', 'pending', 'month', 'prevMonth'), 'Νέα παραγγελία', 'overview');
