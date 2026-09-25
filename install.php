<?php
declare(strict_types=1);

// Εγκατάσταση: τρέχει μία φορά. Φτιάχνει τους πίνακες και τον λογαριασμό διαχειριστή.
require __DIR__ . '/app/bootstrap.php';
require __DIR__ . '/app/migrate.php';
require __DIR__ . '/app/demo.php';

function install_page(string $body): never
{
    $css = asset('app.css');
    echo '<!doctype html><html lang="el"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">'
        . '<title>Εγκατάσταση MyMedia</title><link rel="stylesheet" href="' . e($css) . '"></head><body><div class="auth">'
        . '<header class="hd"><span class="brand">' . tile('logo', 'purple', 28) . ' MyMedia</span></header>'
        . '<div class="wrap narrow">' . $body . '</div></div></body></html>';
    exit;
}

// 1. Σύνδεση με τη βάση
try {
    db();
} catch (PDOException $e) {
    install_page('<h1>Δεν έγινε σύνδεση <em>με τη βάση.</em></h1>'
        . '<p class="lead">Έλεγξε στο <b>config.php</b> το όνομα βάσης, τον χρήστη και τον κωδικό, και ότι ο χρήστης έχει δικαιώματα στη βάση (cPanel → MySQL Databases → Add User To Database → ALL PRIVILEGES).</p>'
        . '<div class="flash error">' . e($e->getMessage()) . '</div>');
}

// 2. Έχει ήδη γίνει εγκατάσταση;
$installed = false;
try {
    $installed = (int) qval('SELECT COUNT(*) FROM users WHERE is_admin = 1') > 0;
} catch (PDOException $e) {
    $installed = false;
}
if ($installed) {
    install_page('<h1>Η εγκατάσταση <em>έχει γίνει.</em></h1>'
        . '<p class="lead">Για ασφάλεια, <b>σβήσε το αρχείο install.php</b> από τον server.</p>'
        . '<a class="btn dark" href="' . e(url('login')) . '">Μετάβαση στην είσοδο</a>');
}

// 3. Φόρμα
$error = null;
$name = '';
$email = '';
if (is_post()) {
    csrf_check();
    $name = input('name');
    $email = mb_strtolower(input('email'));
    $pass = (string) ($_POST['password'] ?? '');
    if (mb_strlen($name) < 2) {
        $error = 'Γράψε το ονοματεπώνυμό σου.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Το email δεν είναι έγκυρο.';
    } else {
        $error = password_problem($pass);
    }
    if (!$error) {
        // 0. Η βάση πρέπει να είναι άδεια ή να έχει μόνο πίνακες του MyMedia (π.χ. από μισή εγκατάσταση).
        //    Δεν αγγίζουμε ποτέ πίνακες άλλης εφαρμογής.
        // Οι πίνακες που φτιάχνουν τα δικά μας migrations (πύλη και εργαλεία)
        $ours = ['migrations'];
        foreach (migration_files() as $file) {
            preg_match_all('/CREATE TABLE (?:IF NOT EXISTS )?`?(\w+)`?/i', (string) file_get_contents($file), $mm);
            $ours = array_merge($ours, $mm[1]);
        }
        $tables = array_map(fn($r) => (string) array_values($r)[0], qall('SHOW TABLES'));
        $foreign = array_values(array_diff($tables, $ours));
        if (in_array('migrations', $tables, true) && !qval("SHOW COLUMNS FROM migrations LIKE 'name'")) {
            $foreign[] = 'migrations';
        }
        if (in_array('businesses', $tables, true)
            && stripos((string) q1("SHOW COLUMNS FROM businesses LIKE 'id'")['Type'], 'int unsigned') !== 0) {
            $foreign[] = 'businesses';
        }
        if ($foreign) {
            install_page('<h1>Η βάση <em>δεν είναι άδεια.</em></h1>'
                . '<p class="lead">Βρέθηκαν πίνακες άλλης εφαρμογής. Για να μην πειραχτούν τα δεδομένα της, το MyMedia χρειάζεται <b>νέα, κενή βάση</b>.</p>'
                . '<div class="flash error" style="word-break:break-word">' . e(implode(', ', array_unique($foreign))) . '</div>'
                . '<p class="body">cPanel → MySQL Databases → φτιάξε νέα βάση, πρόσθεσε σε αυτή τον χρήστη με ALL PRIVILEGES, '
                . 'και γράψε το όνομά της στο <b>config.php</b> (γραμμή <code>\'name\'</code>).</p>'
                . '<a class="btn" href="' . e(url('install.php')) . '">Ξανά</a>');
        }

        // 1. Πίνακες. Αν κάτι αποτύχει, δείχνουμε το ακριβές μήνυμα: εδώ δεν υπάρχουν ακόμα δεδομένα πελατών.
        try {
            run_migrations();
        } catch (Throwable $e) {
            error_log((string) $e);
            install_page('<h1>Η δημιουργία πινάκων <em>απέτυχε.</em></h1>'
                . '<p class="lead">Στείλε αυτό το μήνυμα στον προγραμματιστή. Μπορείς να ξαναδοκιμάσεις με ασφάλεια όποτε θέλεις.</p>'
                . '<div class="flash error" style="word-break:break-word">' . e($e->getMessage()) . '</div>'
                . '<p class="small muted">Βάση: ' . e((string) qval('SELECT VERSION()')) . ' · PHP ' . e(PHP_VERSION) . '</p>'
                . '<a class="btn" href="' . e(url('install.php')) . '">Ξανά</a>');
        }

        // 2. Λογαριασμός διαχειριστή (αν υπάρχει ήδη από προηγούμενη προσπάθεια, γίνεται διαχειριστής)
        $hash = password_hash($pass, PASSWORD_DEFAULT);
        $existing = q1('SELECT id FROM users WHERE email = ?', [$email]);
        if ($existing) {
            q('UPDATE users SET name = ?, password_hash = ?, is_admin = 1, active = 1 WHERE id = ?', [$name, $hash, $existing['id']]);
            $uid = (int) $existing['id'];
        } else {
            q('INSERT INTO users (name, email, password_hash, is_admin) VALUES (?, ?, ?, 1)', [$name, $email, $hash]);
            $uid = (int) db()->lastInsertId();
        }

        // 3. Δείγματα δεδομένων: προαιρετικά, μια αποτυχία εδώ δεν χαλάει την εγκατάσταση
        $demoError = null;
        if (input('demo') === '1') {
            try {
                seed_demo($uid);
            } catch (Throwable $e) {
                error_log((string) $e);
                if (db()->inTransaction()) {
                    db()->rollBack();
                }
                $demoError = $e->getMessage();
            }
        }
        login_user(q1('SELECT * FROM users WHERE id = ?', [$uid]));
        install_page('<h1>Έτοιμο! <em>🎉</em></h1>'
            . '<p class="lead">Η βάση στήθηκε και ο λογαριασμός σου δημιουργήθηκε.</p>'
            . ($demoError ? '<div class="flash error" style="word-break:break-word">Τα δείγματα δεδομένων δεν μπήκαν όλα: ' . e($demoError) . '</div>' : '')
            . '<div class="flash info">Τελευταίο βήμα: <b>σβήσε το αρχείο install.php</b> από τον server (cPanel → File Manager).</div>'
            . '<a class="btn dark" href="' . e(url()) . '">Άνοιγμα της εφαρμογής</a>');
    }
}

$body = '<span class="eyebrow">' . icon('settings', 12) . ' Εγκατάσταση</span>'
    . '<h1>Ας στήσουμε <em>το MyMedia.</em></h1>'
    . '<p class="lead">Η σύνδεση με τη βάση λειτουργεί. Φτιάξε τον λογαριασμό διαχειριστή.</p>'
    . ($error ? '<div class="flash error">' . e($error) . '</div>' : '')
    . '<form class="card" method="post">' . csrf_field()
    . '<label for="name">Ονοματεπώνυμο</label><input id="name" name="name" value="' . e($name) . '" required>'
    . '<label for="email">Email</label><input id="email" name="email" type="email" value="' . e($email) . '" required>'
    . '<label for="password">Κωδικός</label><input id="password" name="password" type="password" minlength="10" autocomplete="new-password" required>'
    . '<p class="hint">Τουλάχιστον 10 χαρακτήρες.</p>'
    . '<label style="display:flex;gap:8px;align-items:flex-start;font-weight:400"><input type="checkbox" name="demo" value="1" checked style="margin-top:3px"> '
    . '<span>Βάλε δείγματα δεδομένων (Καραγκιόζης Club και 3 δοκιμαστικές επιχειρήσεις), για να δεις πώς δουλεύει. Σβήνονται αργότερα από τη Διαχείριση.</span></label>'
    . '<button class="btn dark" type="submit">Εγκατάσταση</button></form>';
install_page($body);
