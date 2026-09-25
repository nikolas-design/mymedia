<?php
// Αντέγραψε αυτό το αρχείο ως config.php και συμπλήρωσε τον κωδικό της βάσης.
// Το config.php δεν ανεβαίνει ποτέ στο GitHub.
return [
    'db' => [
        'host' => 'localhost',
        'name' => 'karagi_mymedia',
        'user' => 'karagi_mymedia',
        'pass' => 'ΒΑΛΕ_ΕΔΩ_ΤΟΝ_ΚΩΔΙΚΟ',
    ],

    // Εμφανίζεται στον τίτλο και στα email
    'app_name' => 'MyMedia',

    // Από ποια διεύθυνση φεύγουν τα email (προσκλήσεις κ.λπ.)
    'mail_from' => 'no-reply@karagiozisclub.gr',

    // true μόνο όσο δοκιμάζεις: δείχνει τα σφάλματα PHP στη σελίδα
    'debug' => false,
];
