<?php
declare(strict_types=1);

/**
 * Migrations: αρχεία .sql που τρέχουν μία φορά το καθένα, με αλφαβητική σειρά.
 *   migrations/NNN_όνομα.sql                 → πύλη
 *   modules/<εργαλείο>/migrations/NNN_*.sql  → κάθε εργαλείο
 * Όσα έχουν τρέξει καταγράφονται στον πίνακα migrations.
 */

function migration_files(): array
{
    $files = [];
    foreach (glob(APP_ROOT . '/migrations/*.sql') ?: [] as $f) {
        $files['core/' . basename($f)] = $f;
    }
    foreach (glob(APP_ROOT . '/modules/*/migrations/*.sql') ?: [] as $f) {
        $files[basename(dirname($f, 2)) . '/' . basename($f)] = $f;
    }
    // Πρώτα η πύλη, μετά τα εργαλεία
    uksort($files, function ($a, $b) {
        $ca = str_starts_with($a, 'core/') ? 0 : 1;
        $cb = str_starts_with($b, 'core/') ? 0 : 1;
        return $ca <=> $cb ?: strcmp($a, $b);
    });
    return $files;
}

function applied_migrations(): array
{
    db()->exec('CREATE TABLE IF NOT EXISTS migrations (
        name VARCHAR(190) PRIMARY KEY,
        applied_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci');
    return array_column(qall('SELECT name FROM migrations'), 'name');
}

function pending_migrations(): array
{
    $done = array_flip(applied_migrations());
    return array_filter(migration_files(), fn($name) => !isset($done[$name]), ARRAY_FILTER_USE_KEY);
}

/** Σπάει ένα αρχείο SQL σε εντολές (διαχωρισμός με ; στο τέλος γραμμής) */
function split_sql(string $sql): array
{
    $lines = array_filter(
        preg_split('/\r?\n/u', $sql),
        fn($l) => !preg_match('/^\s*--/u', $l)
    );
    $parts = preg_split('/;\s*$/mu', implode("\n", $lines));
    return array_values(array_filter(array_map('trim', $parts), fn($s) => $s !== ''));
}

/** Τρέχει όσα λείπουν. Επιστρέφει τα ονόματα που εφαρμόστηκαν. */
function run_migrations(): array
{
    $ran = [];
    foreach (pending_migrations() as $name => $file) {
        foreach (split_sql((string) file_get_contents($file)) as $stmt) {
            db()->exec($stmt);
        }
        q('INSERT INTO migrations (name) VALUES (?)', [$name]);
        $ran[] = $name;
    }
    return $ran;
}
