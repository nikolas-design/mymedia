<?php
// Προβολή του μενού όπως το βλέπουν οι πελάτες, χωρίς να μετράει σάρωση
qr_public_page('public_menu', qr_menu_data($bid) + [
    'profile' => $profile,
    'qr' => ['code' => '', 'table_label' => $pro ? 'Τραπέζι 1' : null, 'type' => 'menu'],
    'canCall' => $pro && $profile['waiter_calls'],
    'preview' => true,
]);
