<?php
if (current_user()) {
    redirect('');
}

$email = '';
$error = null;
if (is_post()) {
    $email = mb_strtolower(input('email'));
    $pass = (string) ($_POST['password'] ?? '');
    if (login_blocked($email)) {
        $error = 'Πολλές αποτυχημένες προσπάθειες. Δοκίμασε ξανά σε 15 λεπτά.';
    } else {
        $u = q1('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
        if ($u && password_verify($pass, $u['password_hash'])) {
            if (password_needs_rehash($u['password_hash'], PASSWORD_DEFAULT)) {
                q('UPDATE users SET password_hash = ? WHERE id = ?', [password_hash($pass, PASSWORD_DEFAULT), $u['id']]);
            }
            login_user($u);
            $next = $_SESSION['after_login'] ?? '';
            unset($_SESSION['after_login']);
            // Μόνο εσωτερικές διαδρομές
            if (is_string($next) && str_starts_with($next, base_path() . '/') && !str_starts_with($next, '//')) {
                header('Location: ' . $next);
                exit;
            }
            redirect('');
        }
        login_failed($email);
        $error = 'Λάθος email ή κωδικός.';
    }
}

render('login', ['email' => $email, 'error' => $error], ['title' => 'Είσοδος', 'area' => 'auth']);
