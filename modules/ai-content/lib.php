<?php
declare(strict_types=1);

const AI_PLATFORMS = ['instagram' => 'Instagram', 'facebook' => 'Facebook', 'tiktok' => 'TikTok', 'google' => 'Google Business'];
const AI_STATUS = ['draft' => ['Πρόταση', 'grey'], 'approved' => ['Εγκρίθηκε', 'purple'], 'posted' => ['Δημοσιεύτηκε', 'green']];
const AI_MONTHLY_RUNS = 20; // παραγωγές ανά μήνα ανά επιχείρηση

function ai_profile(int $businessId): array
{
    $p = q1('SELECT * FROM ai_profiles WHERE business_id = ?', [$businessId]);
    if (!$p) {
        q('INSERT INTO ai_profiles (business_id) VALUES (?)', [$businessId]);
        $p = q1('SELECT * FROM ai_profiles WHERE business_id = ?', [$businessId]);
    }
    return $p;
}

function ai_enabled(): bool
{
    return trim((string) ($GLOBALS['config']['anthropic_api_key'] ?? '')) !== '';
}

function ai_runs_this_month(int $businessId): int
{
    return (int) qval("SELECT COUNT(*) FROM ai_generations WHERE business_id = ? AND status = 'ok' AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", [$businessId]);
}

/**
 * Ζητά από το Claude προτάσεις posts. Επιστρέφει ['posts' => [...], 'usage' => [...], 'model' => ...].
 * Κλήση με απλό HTTP (curl): το shared hosting δεν έχει Composer για το επίσημο SDK.
 */
function ai_generate_posts(array $business, array $profile, array $brief): array
{
    $cfg = $GLOBALS['config'];
    $model = $cfg['anthropic_model'] ?? 'claude-opus-5';

    $platforms = array_values(array_intersect(array_keys(AI_PLATFORMS), explode(',', $brief['platforms'] ?: $profile['platforms'])));
    $schema = [
        'type' => 'object',
        'additionalProperties' => false,
        'required' => ['posts'],
        'properties' => [
            'posts' => [
                'type' => 'array',
                'items' => [
                    'type' => 'object',
                    'additionalProperties' => false,
                    'required' => ['date', 'platform', 'title', 'caption', 'hashtags', 'image_idea'],
                    'properties' => [
                        'date' => ['type' => 'string', 'description' => 'YYYY-MM-DD'],
                        'platform' => ['type' => 'string', 'enum' => $platforms ?: ['instagram']],
                        'title' => ['type' => 'string', 'description' => 'Σύντομος εσωτερικός τίτλος, έως 8 λέξεις'],
                        'caption' => ['type' => 'string'],
                        'hashtags' => ['type' => 'array', 'items' => ['type' => 'string']],
                        'image_idea' => ['type' => 'string', 'description' => 'Τι φωτογραφία ή βίντεο να τραβήξει η επιχείρηση'],
                    ],
                ],
            ],
        ],
    ];

    $system = "Είσαι έμπειρος social media copywriter για μικρές ελληνικές επιχειρήσεις. Γράφεις φυσικά, σύγχρονα ελληνικά, "
        . "όπως θα έγραφε ο ίδιος ο ιδιοκτήτης, χωρίς κλισέ διαφήμισης και χωρίς υπερβολές. Κάθε caption έχει ένα σαφές μήνυμα "
        . "και τελειώνει με ήπιο κάλεσμα σε δράση όταν ταιριάζει. Δεν επινοείς τιμές, προσφορές ή γεγονότα που δεν αναφέρονται "
        . "στα στοιχεία της επιχείρησης ή στο αίτημα. Τα hashtags είναι χωρίς # στην αρχή.";

    $lines = [
        'Επιχείρηση: ' . $business['name'],
        'Περιγραφή: ' . ($profile['description'] ?: '—'),
        'Κοινό: ' . ($profile['audience'] ?: '—'),
        'Ύφος: ' . ($profile['tone'] ?: 'φιλικό, ζεστό'),
        'Προϊόντα / υπηρεσίες: ' . ($profile['products'] ?: '—'),
        'Να αποφεύγεις: ' . ($profile['avoid'] ?: '—'),
        'Σταθερά hashtags: ' . ($profile['hashtags'] ?: '—'),
        'Emojis: ' . ($profile['emojis'] ? 'λίγα, όπου ταιριάζουν' : 'όχι'),
        '',
        'Φτιάξε ' . (int) $brief['count'] . ' posts για τις πλατφόρμες: ' . implode(', ', $platforms ?: ['instagram']) . '.',
        'Ημερομηνίες από ' . $brief['from'] . ' έως ' . $brief['to'] . ', μοιρασμένες λογικά (όχι δύο posts την ίδια μέρα στην ίδια πλατφόρμα).',
        'Σημερινή ημερομηνία: ' . date('Y-m-d') . '. Λάβε υπόψη εποχή, αργίες και γιορτές της Ελλάδας σε αυτό το διάστημα.',
    ];
    if (trim($brief['theme']) !== '') {
        $lines[] = 'Θέμα ή προτεραιότητα από την επιχείρηση: ' . $brief['theme'];
    }

    $body = [
        'model' => $model,
        'max_tokens' => 16000,
        'fallbacks' => 'default',
        'system' => $system,
        'output_config' => ['format' => ['type' => 'json_schema', 'schema' => $schema]],
        'messages' => [['role' => 'user', 'content' => implode("\n", $lines)]],
    ];

    $ch = curl_init('https://api.anthropic.com/v1/messages');
    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 240,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_HTTPHEADER => [
            'content-type: application/json',
            'x-api-key: ' . $cfg['anthropic_api_key'],
            'anthropic-version: 2023-06-01',
            // Αν το μοντέλο αρνηθεί το αίτημα, το API το ξανατρέχει σε άλλο μοντέλο
            'anthropic-beta: server-side-fallback-2026-07-01',
        ],
        CURLOPT_POSTFIELDS => json_encode($body, JSON_UNESCAPED_UNICODE),
    ]);
    @set_time_limit(300);
    $raw = curl_exec($ch);
    $status = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($raw === false) {
        throw new RuntimeException('Δεν έγινε σύνδεση με την υπηρεσία AI: ' . $err);
    }
    $res = json_decode((string) $raw, true);
    if ($status !== 200 || !is_array($res)) {
        $msg = $res['error']['message'] ?? ('HTTP ' . $status);
        throw new RuntimeException(match (true) {
            $status === 429, $status === 529 => 'Η υπηρεσία AI είναι φορτωμένη. Δοκίμασε ξανά σε λίγα λεπτά.',
            $status === 401 => 'Το κλειδί της υπηρεσίας AI δεν είναι έγκυρο.',
            default => 'Σφάλμα υπηρεσίας AI: ' . $msg,
        });
    }
    if (($res['stop_reason'] ?? '') === 'refusal') {
        throw new RuntimeException('Το AI δεν μπόρεσε να απαντήσει σε αυτό το αίτημα. Άλλαξε λίγο το θέμα και δοκίμασε ξανά.');
    }
    if (($res['stop_reason'] ?? '') === 'max_tokens') {
        throw new RuntimeException('Η απάντηση ήταν πολύ μεγάλη. Ζήτα λιγότερα posts.');
    }
    $text = '';
    foreach ($res['content'] ?? [] as $block) {
        if (($block['type'] ?? '') === 'text') {
            $text .= $block['text'];
        }
    }
    $data = json_decode($text, true);
    if (!is_array($data['posts'] ?? null)) {
        throw new RuntimeException('Η απάντηση του AI δεν ήταν στη σωστή μορφή. Δοκίμασε ξανά.');
    }
    return ['posts' => $data['posts'], 'usage' => $res['usage'] ?? [], 'model' => $res['model'] ?? $model];
}
