<?php
$user = require_login();
if (is_post()) {
    $id = input_int('business_id');
    foreach (user_businesses((int) $user['id']) as $b) {
        if ((int) $b['id'] === $id) {
            $_SESSION['bid'] = $id;
            flash('Βλέπεις τώρα: ' . $b['name'], 'info');
        }
    }
}
redirect('dashboard');
