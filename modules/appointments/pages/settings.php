<?php
module_require_edit();
if (is_post()) {
    q('UPDATE ap_settings SET title = ?, intro = ?, color = ?, slot_minutes = ?, min_notice_hours = ?, max_days_ahead = ?, auto_confirm = ?, cancel_hours = ?, phone = ?, address = ? WHERE business_id = ?', [
        mb_substr(input('title'), 0, 120) ?: $business['name'], mb_substr(input('intro'), 0, 255) ?: null,
        preg_match('/^#[0-9a-fA-F]{6}$/', input('color')) ? input('color') : '#d64b7c',
        in_array(input_int('slot_minutes'), [5, 10, 15, 20, 30, 60], true) ? input_int('slot_minutes') : 15,
        max(0, min(168, input_int('min_notice_hours'))), max(1, min(365, input_int('max_days_ahead'))),
        input('auto_confirm') === '1' ? 1 : 0, max(0, min(168, input_int('cancel_hours'))),
        mb_substr(input('phone'), 0, 40) ?: null, mb_substr(input('address'), 0, 190) ?: null, $bid,
    ]);
    flash('Αποθηκεύτηκε.');
    redirect('t/appointments/settings');
}
$settings = ap_settings($bid);
module_page('settings', compact('settings'), 'Σελίδα κρατήσεων', 'settings');
