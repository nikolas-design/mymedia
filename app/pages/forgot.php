<?php
$sent = false;
if (is_post()) {
    $email = mb_strtolower(input('email'));
    $u = q1('SELECT * FROM users WHERE email = ? AND active = 1', [$email]);
    // Ίδιο μήνυμα είτε υπάρχει ο λογαριασμός είτε όχι
    $sent = true;
    if ($u && !login_blocked($email)) {
        login_failed($email); // μετράει ως προσπάθεια, για να μη γίνεται κατάχρηση
        $token = bin2hex(random_bytes(32));
        q('INSERT INTO password_resets (user_id, token_hash, expires_at) VALUES (?, ?, NOW() + INTERVAL 1 HOUR)',
            [$u['id'], hash('sha256', $token)]);
        send_mail($u['email'], 'Νέος κωδικός για το ' . ($GLOBALS['config']['app_name'] ?? 'MyMedia'),
            "Γεια σου {$u['name']},\n\nΓια να ορίσεις νέο κωδικό, άνοιξε τον σύνδεσμο (ισχύει 1 ώρα):\n"
            . full_url('reset/' . $token) . "\n\nΑν δεν το ζήτησες εσύ, αγνόησε αυτό το μήνυμα.");
    }
}
render('forgot', ['sent' => $sent], ['title' => 'Ξέχασα τον κωδικό', 'area' => 'auth']);
