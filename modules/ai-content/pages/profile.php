<?php
module_require_edit();
if (is_post()) {
    q('UPDATE ai_profiles SET description = ?, audience = ?, tone = ?, products = ?, avoid = ?, hashtags = ?, platforms = ?, emojis = ? WHERE business_id = ?', [
        mb_substr(input('description'), 0, 3000) ?: null, mb_substr(input('audience'), 0, 255) ?: null, mb_substr(input('tone'), 0, 120) ?: null,
        mb_substr(input('products'), 0, 3000) ?: null, mb_substr(input('avoid'), 0, 255) ?: null, mb_substr(input('hashtags'), 0, 255) ?: null,
        implode(',', array_intersect(array_keys(AI_PLATFORMS), (array) ($_POST['platforms'] ?? []))) ?: 'instagram', input('emojis') === '1' ? 1 : 0, $bid,
    ]);
    flash('Αποθηκεύτηκε. Οι επόμενες προτάσεις θα γράφονται με αυτό το ύφος.');
    redirect('t/ai-content/profile');
}
module_page('profile', [], 'Το ύφος σου', 'profile');
