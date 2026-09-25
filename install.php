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
        run_migrations();
        q('INSERT INTO users (name, email, password_hash, is_admin) VALUES (?, ?, ?, 1)', [$name, $email, password_hash($pass, PASSWORD_DEFAULT)]);
        $uid = (int) db()->lastInsertId();
        if (input('demo') === '1') {
            seed_demo($uid);
        }
        login_user(q1('SELECT * FROM users WHERE id = ?', [$uid]));
        install_page('<h1>Έτοιμο! <em>🎉</em></h1>'
            . '<p class="lead">Η βάση στήθηκε και ο λογαριασμός σου δημιουργήθηκε.</p>'
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
