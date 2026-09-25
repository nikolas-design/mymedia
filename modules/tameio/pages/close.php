<?php
$day = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['day'] ?? '')) ? $_GET['day'] : date('Y-m-d');
if (is_post()) {
    $day = preg_match('/^\d{4}-\d{2}-\d{2}$/', input('day')) ? input('day') : date('Y-m-d');
    if ($day > date('Y-m-d')) {
        flash('Δεν γίνεται κλείσιμο για μελλοντική ημέρα.', 'error');
        redirect('t/tameio/close');
    }
    $shift = mb_substr(input('shift'), 0, 40) ?: 'Ημέρα';
    $cashExp = (int) qval("SELECT COALESCE(SUM(amount_cents),0) FROM tm_expenses WHERE business_id = ? AND day = ? AND payment = 'cash'", [$bid, $day]);
    // Αν υπάρχουν πολλές βάρδιες την ίδια μέρα, τα έξοδα μετρητών μετράνε μία φορά (στο πρώτο κλείσιμο)
    $already = (int) qval('SELECT COALESCE(SUM(cash_expenses_cents),0) FROM tm_closings WHERE business_id = ? AND day = ? AND shift <> ?', [$bid, $day, $shift]);
    $data = [
        parse_money(input('opening')), parse_money(input('cash')), parse_money(input('card')), parse_money(input('other')),
        max(0, $cashExp - $already), input('counted') === '' ? null : parse_money(input('counted')), mb_substr(input('notes'), 0, 500) ?: null, $user['id'],
    ];
    $existing = q1('SELECT id FROM tm_closings WHERE business_id = ? AND day = ? AND shift = ?', [$bid, $day, $shift]);
    if ($existing) {
        q('UPDATE tm_closings SET opening_cents = ?, cash_cents = ?, card_cents = ?, other_cents = ?, cash_expenses_cents = ?, counted_cents = ?, notes = ?, closed_by = ? WHERE id = ?',
            [...$data, $existing['id']]);
        $id = (int) $existing['id'];
    } else {
        q('INSERT INTO tm_closings (opening_cents, cash_cents, card_cents, other_cents, cash_expenses_cents, counted_cents, notes, closed_by, business_id, day, shift)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [...$data, $bid, $day, $shift]);
        $id = (int) db()->lastInsertId();
    }
    flash('Το ταμείο έκλεισε.');
    redirect('t/tameio/closings/' . $id);
}
$shifts = array_column(qall('SELECT DISTINCT shift FROM tm_closings WHERE business_id = ? ORDER BY shift', [$bid]), 'shift') ?: ['Ημέρα'];
$cashExp = (int) qval("SELECT COALESCE(SUM(amount_cents),0) FROM tm_expenses WHERE business_id = ? AND day = ? AND payment = 'cash'", [$bid, $day]);
$lastCounted = qval('SELECT counted_cents FROM tm_closings WHERE business_id = ? AND counted_cents IS NOT NULL ORDER BY day DESC, id DESC LIMIT 1', [$bid]);
module_page('close', compact('day', 'shifts', 'cashExp', 'lastCounted'), 'Κλείσιμο ταμείου', 'close');
