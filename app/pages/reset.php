<?php
$token = $params[0];
$row = q1('SELECT r.*, u.email FROM password_resets r JOIN users u ON u.id = r.user_id
           WHERE r.token_hash = ? AND r.used_at IS NULL AND r.expires_at > NOW()', [hash('sha256', $token)]);
if (!$row) {
    render('message', ['heading' => 'Ο σύνδεσμος έληξε', 'text' => 'Ζήτησε νέο σύνδεσμο από τη σελίδα εισόδου.'],
        ['title' => 'Νέος κωδικός', 'area' => 'auth']);
}
$error = null;
if (is_post()) {
    $pass = (string) ($_POST['password'] ?? '');
    $error = password_problem($pass);
    if (!$error && $pass !== ($_POST['password2'] ?? '')) {
        $error = 'Οι δύο κωδικοί δεν ταιριάζουν.';
    }
    if (!$error) {
        q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $row['user_id']]);
        q('UPDATE password_resets SET used_at = NOW() WHERE user_id = ? AND used_at IS NULL', [$row['user_id']]);
        flash('Ο κωδικός άλλαξε. Συνδέσου με τον νέο.');
        redirect('login');
    }
}
render('reset', ['error' => $error, 'email' => $row['email']], ['title' => 'Νέος κωδικός', 'area' => 'auth']);
