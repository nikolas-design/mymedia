<?php
qr_require_edit();
if (is_post()) {
    $p = $profile;
    try {
        foreach (['logo' => 400, 'cover' => 1600] as $field => $size) {
            $new = store_image($field, 'qr-boss/' . $bid, $size);
            if ($new || input('remove_' . $field) === '1') {
                delete_upload($p[$field]);
                $p[$field] = $new;
            }
        }
    } catch (RuntimeException $e) {
        flash($e->getMessage(), 'error');
        redirect('t/qr-boss/settings');
    }
    $review = input('google_review_url') === '' ? null : qr_clean_url(input('google_review_url'));
    q('UPDATE qr_profiles SET title = ?, subtitle = ?, color = ?, logo = ?, cover = ?, phone = ?, address = ?, hours = ?,
         wifi_ssid = ?, wifi_pass = ?, instagram = ?, facebook = ?, google_review_url = ?, footer = ?, waiter_calls = ? WHERE business_id = ?', [
        mb_substr(input('title'), 0, 120) ?: $business['name'],
        mb_substr(input('subtitle'), 0, 190) ?: null,
        qr_valid_color(input('color')),
        $p['logo'], $p['cover'],
        mb_substr(input('phone'), 0, 40) ?: null,
        mb_substr(input('address'), 0, 190) ?: null,
        mb_substr(input('hours'), 0, 190) ?: null,
        mb_substr(input('wifi_ssid'), 0, 64) ?: null,
        mb_substr(input('wifi_pass'), 0, 64) ?: null,
        input('instagram') === '' ? null : qr_clean_url(input('instagram')),
        input('facebook') === '' ? null : qr_clean_url(input('facebook')),
        $review,
        mb_substr(input('footer'), 0, 255) ?: null,
        input('waiter_calls') === '1' ? 1 : 0,
        $bid,
    ]);
    flash('Η εμφάνιση αποθηκεύτηκε.');
    redirect('t/qr-boss/settings');
}
qr_page('settings', [], 'Εμφάνιση', 'settings');
