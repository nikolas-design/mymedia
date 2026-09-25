<?php
$token = $params[0];
$inv = q1('SELECT i.*, b.name AS business_name FROM invitations i JOIN businesses b ON b.id = i.business_id
           WHERE i.token_hash = ? AND i.accepted_at IS NULL AND i.expires_at > NOW()', [hash('sha256', $token)]);
if (!$inv) {
    render('message', [
        'heading' => 'Η πρόσκληση δεν ισχύει',
        'text'    => 'Μπορεί να έχει λήξει ή να έχει ήδη χρησιμοποιηθεί. Ζήτα νέα πρόσκληση.',
    ], ['title' => 'Πρόσκληση', 'area' => 'auth']);
}

$existing = q1('SELECT * FROM users WHERE email = ?', [$inv['email']]);
$me = current_user();

function accept_invitation(array $inv, int $userId): void
{
    q('INSERT INTO memberships (business_id, user_id, role, job_title, active) VALUES (?, ?, ?, ?, 1)
       ON DUPLICATE KEY UPDATE role = VALUES(role), job_title = VALUES(job_title), active = 1',
        [$inv['business_id'], $userId, $inv['role'], $inv['job_title']]);
    q('UPDATE invitations SET accepted_at = NOW() WHERE id = ?', [$inv['id']]);
    $_SESSION['bid'] = (int) $inv['business_id'];
}

$error = null;
if (is_post()) {
    if ($existing) {
        // Ο λογαριασμός υπάρχει: πρέπει να είναι συνδεδεμένος ο ίδιος
        if ($me && (int) $me['id'] === (int) $existing['id']) {
            accept_invitation($inv, (int) $me['id']);
            flash('Καλώς ήρθες στην ομάδα της επιχείρησης ' . $inv['business_name'] . '.');
            redirect('dashboard');
        }
        forbidden();
    }
    $name = input('name');
    $pass = (string) ($_POST['password'] ?? '');
    if (mb_strlen($name) < 2) {
        $error = 'Γράψε το ονοματεπώνυμό σου.';
    } else {
        $error = password_problem($pass);
    }
    if (!$error) {
        q('INSERT INTO users (name, email, password_hash) VALUES (?, ?, ?)', [$name, $inv['email'], password_hash($pass, PASSWORD_DEFAULT)]);
        $uid = (int) db()->lastInsertId();
        login_user(q1('SELECT * FROM users WHERE id = ?', [$uid]));
        accept_invitation($inv, $uid);
        flash('Ο λογαριασμός σου δημιουργήθηκε. Καλώς ήρθες!');
        redirect('dashboard');
    }
}

render('invite', [
    'inv' => $inv, 'existing' => $existing, 'me' => $me, 'error' => $error,
], ['title' => 'Πρόσκληση', 'area' => 'auth']);
