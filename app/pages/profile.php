<?php
$me = require_login();
$biz = current_business();

if (is_post()) {
    $action = input('action');
    if ($action === 'profile') {
        $name = input('name');
        if (mb_strlen($name) < 2) {
            flash('Γράψε το ονοματεπώνυμό σου.', 'error');
        } else {
            q('UPDATE users SET name = ? WHERE id = ?', [$name, $me['id']]);
            flash('Το προφίλ αποθηκεύτηκε.');
        }
    } elseif ($action === 'password') {
        $cur = (string) ($_POST['current'] ?? '');
        $new = (string) ($_POST['password'] ?? '');
        if (!password_verify($cur, $me['password_hash'])) {
            flash('Ο τρέχων κωδικός δεν είναι σωστός.', 'error');
        } elseif ($err = password_problem($new)) {
            flash($err, 'error');
        } else {
            q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($new, PASSWORD_DEFAULT), $me['id']]);
            session_regenerate_id(true);
            flash('Ο κωδικός άλλαξε.');
        }
    }
    redirect('profile');
}

$businesses = user_businesses((int) $me['id']);
render('profile', compact('me', 'biz', 'businesses'), [
    'title' => 'Προφίλ', 'nav' => 'profile', 'area' => $biz ? 'client' : 'admin',
]);
