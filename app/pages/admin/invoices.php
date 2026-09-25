<?php
require_admin();

if (is_post()) {
    $inv = q1('SELECT * FROM invoices WHERE id = ?', [input_int('id')]);
    if ($inv) {
        if (input('do') === 'paid' && $inv['status'] === 'issued') {
            q("UPDATE invoices SET status = 'paid', paid_on = CURDATE() WHERE id = ?", [$inv['id']]);
            notify((int) $inv['business_id'], 'Λάβαμε την πληρωμή για το ' . $inv['number'] . '. Ευχαριστούμε!', 'invoices/' . $inv['id']);
            flash('Σημειώθηκε ως πληρωμένο.');
        } elseif (input('do') === 'unpaid' && $inv['status'] === 'paid') {
            q("UPDATE invoices SET status = 'issued', paid_on = NULL WHERE id = ?", [$inv['id']]);
            flash('Σημειώθηκε ως απλήρωτο.', 'info');
        } elseif (input('do') === 'void' && $inv['status'] === 'issued') {
            q("UPDATE invoices SET status = 'void' WHERE id = ?", [$inv['id']]);
            flash('Το παραστατικό ακυρώθηκε.', 'info');
        }
    }
    redirect('admin/invoices' . (isset($_GET['status']) ? '?status=' . urlencode((string) $_GET['status']) : ''));
}

$status = (string) ($_GET['status'] ?? '');
$args = [];
$where = '1=1';
if (in_array($status, ['issued', 'paid', 'void'], true)) {
    $where = 'i.status = ?';
    $args[] = $status;
}
$invoices = qall("SELECT i.*, b.name AS business_name FROM invoices i JOIN businesses b ON b.id = i.business_id
                  WHERE $where ORDER BY i.issued_on DESC, i.id DESC LIMIT 300", $args);
$totals = q1("SELECT COALESCE(SUM(CASE WHEN status='issued' THEN total_cents END),0) AS unpaid,
                     COALESCE(SUM(CASE WHEN status='paid' AND paid_on >= DATE_FORMAT(CURDATE(), '%Y-%m-01') THEN total_cents END),0) AS paid_month
              FROM invoices");
render('admin/invoices', compact('invoices', 'status', 'totals'), ['title' => 'Παραστατικά', 'nav' => 'admin-invoices', 'area' => 'admin']);
