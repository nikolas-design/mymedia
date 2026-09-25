<?php
if (is_post()) {
    logout_user();
    flash('Αποσυνδέθηκες.', 'info');
}
redirect('login');
