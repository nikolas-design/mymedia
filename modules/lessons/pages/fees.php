<?php
module_require_edit();
$month = preg_match('/^\d{4}-\d{2}$/', (string) ($_REQUEST['m'] ?? '')) ? $_REQUEST['m'] : date('Y-m');
if (is_post() && input('action') === 'generate') {
    // Χρέωση διδάκτρων του μήνα για κάθε εγγραφή (μία φορά ανά τμήμα)
    $n = 0;
    foreach (qall('SELECT e.student_id, g.name, COALESCE(e.fee_cents, g.monthly_fee_cents) AS fee FROM ls_enrollments e JOIN ls_groups g ON g.id = e.group_id
                   JOIN ls_students s ON s.id = e.student_id WHERE g.business_id = ? AND g.active = 1 AND s.active = 1', [$bid]) as $r) {
        if ((int) $r['fee'] > 0) {
            $n += q('INSERT IGNORE INTO ls_charges (business_id, student_id, month, description, amount_cents) VALUES (?, ?, ?, ?, ?)',
                [$bid, $r['student_id'], $month, 'Δίδακτρα ' . $r['name'], $r['fee']])->rowCount();
        }
    }
    flash($n ? "Δημιουργήθηκαν $n χρεώσεις για τον μήνα $month." : 'Οι χρεώσεις του μήνα υπάρχουν ήδη.', $n ? 'ok' : 'info');
    redirect('t/lessons/fees?m=' . $month);
}
$rows = qall('SELECT s.id, s.name, s.parent_name, s.phone, SUM(c.amount_cents) AS amount, SUM(c.paid_cents) AS paid FROM ls_charges c JOIN ls_students s ON s.id = c.student_id
              WHERE c.business_id = ? AND c.month = ? GROUP BY s.id, s.name, s.parent_name, s.phone ORDER BY (SUM(c.amount_cents) - SUM(c.paid_cents)) DESC, s.name', [$bid, $month]);
$totalOwed = (int) qval('SELECT COALESCE(SUM(amount_cents - paid_cents),0) FROM ls_charges WHERE business_id = ?', [$bid]);
module_page('fees', compact('month', 'rows', 'totalOwed'), 'Δίδακτρα', 'fees');
