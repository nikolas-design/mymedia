<?php
if (is_post()) {
    $amount = parse_money(input('amount'));
    if ($amount <= 0) {
        flash('Γράψε ποσό.', 'error');
        redirect('t/tameio/expenses');
    }
    try {
        $photo = store_image('photo', 'tameio/' . $bid, 1600);
    } catch (RuntimeException $e) {
        flash($e->getMessage(), 'error');
        redirect('t/tameio/expenses');
    }
    $day = preg_match('/^\d{4}-\d{2}-\d{2}$/', input('day')) ? input('day') : date('Y-m-d');
    q('INSERT INTO tm_expenses (business_id, day, category, description, amount_cents, payment, photo, created_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [
        $bid, $day, in_array(input('category'), TM_CATEGORIES, true) ? input('category') : 'Διάφορα', mb_substr(input('description'), 0, 190) ?: null,
        $amount, array_key_exists(input('payment'), TM_PAYMENT) ? input('payment') : 'cash', $photo, $user['id'],
    ]);
    flash('Το έξοδο καταχωρήθηκε.');
    redirect('t/tameio/expenses');
}
$month = preg_match('/^\d{4}-\d{2}$/', (string) ($_GET['m'] ?? '')) ? $_GET['m'] : date('Y-m');
$expenses = qall('SELECT x.*, u.name AS by_name FROM tm_expenses x LEFT JOIN users u ON u.id = x.created_by
                  WHERE x.business_id = ? AND x.day >= ? AND x.day < ? ORDER BY x.day DESC, x.id DESC',
    [$bid, $month . '-01', date('Y-m-01', strtotime($month . '-01 +1 month'))]);
module_page('expenses', compact('expenses', 'month'), 'Έξοδα', 'expenses');
