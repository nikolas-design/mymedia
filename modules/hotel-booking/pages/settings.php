<?php
module_require_edit();
if (is_post()) {
    q('UPDATE hb_settings SET title = ?, color = ?, checkin = ?, checkout = ?, min_nights = ?, auto_confirm = ?, deposit_percent = ?, policy = ?, phone = ?, email = ?, address = ? WHERE business_id = ?', [
        mb_substr(input('title'), 0, 120) ?: $business['name'], preg_match('/^#[0-9a-fA-F]{6}$/', input('color')) ? input('color') : '#22a06b',
        mb_substr(input('checkin'), 0, 10) ?: '14:00', mb_substr(input('checkout'), 0, 10) ?: '11:00', max(1, min(30, input_int('min_nights'))),
        input('auto_confirm') === '1' ? 1 : 0, max(0, min(100, input_int('deposit_percent'))), mb_substr(input('policy'), 0, 3000) ?: null,
        input('phone') ?: null, filter_var(input('email'), FILTER_VALIDATE_EMAIL) ? input('email') : null, input('address') ?: null, $bid,
    ]);
    flash('Αποθηκεύτηκε.');
    redirect('t/hotel-booking/settings');
}
$settings = hb_settings($bid);
module_page('settings', compact('settings'), 'Μηχανή κρατήσεων', 'settings');
