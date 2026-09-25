<?php
module_require_edit();
$used = ai_runs_this_month($bid);
$enabled = ai_enabled();
if (is_post()) {
    if (!$enabled) {
        flash('Η παραγωγή με AI δεν έχει ενεργοποιηθεί ακόμα.', 'error');
        redirect('t/ai-content/generate');
    }
    if ($used >= AI_MONTHLY_RUNS) {
        flash('Έφτασες το όριο των ' . AI_MONTHLY_RUNS . ' παραγωγών αυτόν τον μήνα.', 'error');
        redirect('t/ai-content/generate');
    }
    $from = preg_match('/^\d{4}-\d{2}-\d{2}$/', input('from')) ? input('from') : date('Y-m-d', strtotime('+1 day'));
    $to = preg_match('/^\d{4}-\d{2}-\d{2}$/', input('to')) && input('to') >= $from ? input('to') : date('Y-m-d', strtotime("$from +6 days"));
    $platforms = implode(',', array_intersect(array_keys(AI_PLATFORMS), (array) ($_POST['platforms'] ?? [])));
    $brief = ['from' => $from, 'to' => $to, 'count' => max(1, min(12, input_int('count'))), 'theme' => mb_substr(input('theme'), 0, 500), 'platforms' => $platforms];
    try {
        $out = ai_generate_posts($business, $profile, $brief);
    } catch (RuntimeException $e) {
        q("INSERT INTO ai_generations (business_id, user_id, brief, status, error) VALUES (?, ?, ?, 'error', ?)",
            [$bid, $user['id'], $brief['theme'] ?: null, mb_substr($e->getMessage(), 0, 500)]);
        flash($e->getMessage(), 'error');
        redirect('t/ai-content/generate');
    }
    $n = 0;
    foreach ($out['posts'] as $p) {
        $caption = trim((string) ($p['caption'] ?? ''));
        if ($caption === '') {
            continue;
        }
        $date = preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($p['date'] ?? '')) ? $p['date'] : null;
        $tags = implode(' ', array_map(fn($t) => '#' . ltrim(preg_replace('/\s+/u', '', (string) $t), '#'), array_slice((array) ($p['hashtags'] ?? []), 0, 15)));
        q('INSERT INTO ai_posts (business_id, for_date, platform, title, caption, hashtags, image_idea) VALUES (?, ?, ?, ?, ?, ?, ?)', [
            $bid, $date, array_key_exists($p['platform'] ?? '', AI_PLATFORMS) ? $p['platform'] : 'instagram',
            mb_substr((string) ($p['title'] ?? ''), 0, 120) ?: null, $caption, mb_substr($tags, 0, 500) ?: null, mb_substr((string) ($p['image_idea'] ?? ''), 0, 500) ?: null,
        ]);
        $n++;
    }
    q('INSERT INTO ai_generations (business_id, user_id, brief, posts, model, input_tokens, output_tokens) VALUES (?, ?, ?, ?, ?, ?, ?)', [
        $bid, $user['id'], $brief['theme'] ?: null, $n, $out['model'], (int) ($out['usage']['input_tokens'] ?? 0), (int) ($out['usage']['output_tokens'] ?? 0),
    ]);
    flash("Έτοιμες $n προτάσεις! Διάβασέ τες, άλλαξε ό,τι θέλεις και έγκρινε.");
    redirect('t/ai-content?status=draft');
}
module_page('generate', compact('used', 'enabled'), 'Νέες προτάσεις', 'generate');
