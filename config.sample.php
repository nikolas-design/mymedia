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

    // Η πλήρης διεύθυνση της εφαρμογής (για τα email που στέλνει το cron)
    'base_url' => 'https://www.karagiozisclub.gr/mymedia',

    // Κλειδί για το cron.php όταν καλείται από URL (άλλαξέ το σε κάτι τυχαίο)
    'cron_key' => 'ΑΛΛΑΞΕ_ΜΕ_ΤΥΧΑΙΟ_ΚΕΙΜΕΝΟ',

    // AI Content: κλειδί από το console.anthropic.com (κενό = το AI είναι ανενεργό)
    'anthropic_api_key' => '',
    'anthropic_model' => 'claude-opus-5',

    // true μόνο όσο δοκιμάζεις: δείχνει τα σφάλματα PHP στη σελίδα
    'debug' => false,
];
