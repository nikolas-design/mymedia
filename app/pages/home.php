<?php
if (!current_user()) {
    redirect('login');
}
if (current_business()) {
    redirect('dashboard');
}
redirect(is_admin() ? 'admin' : 'dashboard');
