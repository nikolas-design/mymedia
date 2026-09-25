<?php
$o = q1('SELECT * FROM ro_orders WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$o) {
    not_found();
}
if (is_post()) {
    $do = input('do');
    $flow = ['accept' => ['new', 'accepted'], 'ready' => ['accepted', 'ready'], 'out' => ['accepted', 'out'], 'completed' => [null, 'completed'], 'reject' => ['new', 'rejected']];
    if (isset($flow[$do]) && ($flow[$do][0] === null ? in_array($o['status'], ['accepted', 'ready', 'out'], true) : $o['status'] === $flow[$do][0])) {
        $eta = $do === 'accept' ? max(5, min(180, input_int('eta') ?: (int) $settings['prep_minutes'])) : $o['eta_minutes'];
        q("UPDATE ro_orders SET status = ?, eta_minutes = ?, reject_reason = ?, accepted_at = IF(? = 'accepted', NOW(), accepted_at) WHERE id = ?",
            [$flow[$do][1], $eta, $do === 'reject' ? (mb_substr(input('reason'), 0, 190) ?: null) : $o['reject_reason'], $flow[$do][1], $o['id']]);
    }
    $back = $_SERVER['HTTP_REFERER'] ?? '';
    redirect(str_contains($back, '/orders/') ? 't/restaurant-ordering/orders/' . $o['id'] : 't/restaurant-ordering');
}
$lines = qall('SELECT * FROM ro_order_lines WHERE order_id = ? ORDER BY id', [$o['id']]);
module_page('order', compact('o', 'lines', 'settings'), 'Παραγγελία #' . $o['number'], 'orders');
