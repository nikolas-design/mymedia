<?php
$isNew = $params[0] === 'new';
$p = $isNew ? null : q1('SELECT * FROM ai_posts WHERE id = ? AND business_id = ?', [(int) $params[0], $bid]);
if (!$isNew && !$p) {
    not_found();
}
if (is_post()) {
    module_require_edit();
    if (!$isNew && input('action') === 'delete') {
        q('DELETE FROM ai_posts WHERE id = ?', [$p['id']]);
        flash('Διαγράφηκε.', 'info');
        redirect('t/ai-content');
    }
    $caption = trim(input('caption'));
    if ($caption === '') {
        flash('Το κείμενο είναι κενό.', 'error');
        redirect('t/ai-content/posts/' . ($isNew ? 'new' : $p['id']));
    }
    $data = [
        preg_match('/^\d{4}-\d{2}-\d{2}$/', input('for_date')) ? input('for_date') : null,
        array_key_exists(input('platform'), AI_PLATFORMS) ? input('platform') : 'instagram',
        mb_substr(input('title'), 0, 120) ?: null, $caption, mb_substr(input('hashtags'), 0, 500) ?: null, mb_substr(input('image_idea'), 0, 500) ?: null,
        array_key_exists(input('status'), AI_STATUS) ? input('status') : 'draft',
    ];
    if ($isNew) {
        q("INSERT INTO ai_posts (for_date, platform, title, caption, hashtags, image_idea, status, business_id, source) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'manual')", [...$data, $bid]);
    } else {
        q('UPDATE ai_posts SET for_date = ?, platform = ?, title = ?, caption = ?, hashtags = ?, image_idea = ?, status = ? WHERE id = ?', [...$data, $p['id']]);
    }
    flash('Αποθηκεύτηκε.');
    redirect('t/ai-content');
}
module_page('post', compact('p', 'isNew'), $isNew ? 'Νέο post' : ($p['title'] ?: 'Post'), 'posts');
