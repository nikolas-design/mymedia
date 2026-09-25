<?php
module_require_edit();
if (is_post()) {
    q('UPDATE ro_settings SET title = ?, color = ?, accepting = ?, delivery = ?, takeaway = ?, min_order_cents = ?, delivery_fee_cents = ?, areas = ?, prep_minutes = ?, phone = ?, address = ?, hours = ?, note = ? WHERE business_id = ?', [
        mb_substr(input('title'), 0, 120) ?: $business['name'], preg_match('/^#[0-9a-fA-F]{6}$/', input('color')) ? input('color') : '#c2410c',
        input('accepting') === '1' ? 1 : 0, input('delivery') === '1' ? 1 : 0, input('takeaway') === '1' ? 1 : 0,
        parse_money(input('min_order')), parse_money(input('delivery_fee')), mb_substr(input('areas'), 0, 500) ?: null,
        max(5, min(180, input_int('prep_minutes'))), input('phone') ?: null, input('address') ?: null, mb_substr(input('hours'), 0, 190) ?: null, mb_substr(input('note'), 0, 255) ?: null, $bid,
    ]);
    flash('Αποθηκεύτηκε.');
    redirect('t/restaurant-ordering/settings');
}
$settings = ro_settings($bid);
module_page('settings', compact('settings'), 'Ρυθμίσεις', 'settings');
