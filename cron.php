<?php
declare(strict_types=1);

/**
 * Αυτόματες εργασίες (υπενθυμίσεις, αναφορές). Τρέχει κάθε 15 λεπτά από το cPanel → Cron Jobs:
 *   php /home/<χρήστης>/public_html/mymedia/cron.php
 * ή, αν δεν γίνεται με php, με URL:
 *   wget -q -O- "https://www.karagiozisclub.gr/mymedia/cron.php?key=<cron_key>"
 *
 * Κάθε εργαλείο που χρειάζεται αυτόματες εργασίες έχει modules/<slug>/cron.php.
 */
require __DIR__ . '/app/bootstrap.php';

if (PHP_SAPI !== 'cli') {
    $key = (string) ($GLOBALS['config']['cron_key'] ?? '');
    if ($key === '' || str_starts_with($key, 'ΑΛΛΑΞΕ') || !hash_equals($key, (string) ($_GET['key'] ?? ''))) {
        http_response_code(403);
        exit('forbidden');
    }
    header('Content-Type: text/plain; charset=utf-8');
}

@set_time_limit(300);
$log = [];
foreach (glob(APP_ROOT . '/modules/*/cron.php') ?: [] as $file) {
    $slug = basename(dirname($file));
    try {
        // Κάθε cron.php επιστρέφει ένα σύντομο κείμενο με το τι έκανε
        $out = (function () use ($file) {
            return require $file;
        })();
        $log[] = "$slug: " . (is_string($out) ? $out : 'ok');
    } catch (Throwable $e) {
        error_log("cron $slug: " . $e);
        $log[] = "$slug: ΣΦΑΛΜΑ " . $e->getMessage();
    }
}
q('INSERT INTO settings (k, v) VALUES (?, ?) ON DUPLICATE KEY UPDATE v = VALUES(v)', ['cron_last_run', date('Y-m-d H:i:s')]);
echo implode("\n", $log) . "\n";
