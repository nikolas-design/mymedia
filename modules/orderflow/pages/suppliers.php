<?php
$suppliers = qall('SELECT s.*, (SELECT COUNT(*) FROM of_products p WHERE p.supplier_id = s.id) AS products
                   FROM of_suppliers s WHERE s.business_id = ? ORDER BY s.active DESC, s.name', [$bid]);
module_page('suppliers', compact('suppliers'), 'Προμηθευτές', 'suppliers');
