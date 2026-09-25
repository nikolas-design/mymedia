<?php
declare(strict_types=1);

const RO_STATUS = [
    'new'       => ['Νέα', 'orange'],
    'accepted'  => ['Ετοιμάζεται', 'purple'],
    'ready'     => ['Έτοιμη για παραλαβή', 'green'],
    'out'       => ['Στον δρόμο', 'purple'],
    'completed' => ['Ολοκληρώθηκε', 'green'],
    'rejected'  => ['Απορρίφθηκε', 'grey'],
];
const RO_KIND = ['delivery' => 'Delivery', 'takeaway' => 'Take away'];

function ro_settings(int $businessId): array
{
    $s = q1('SELECT * FROM ro_settings WHERE business_id = ?', [$businessId]);
    if (!$s) {
        do {
            $code = short_code();
        } while (qval('SELECT 1 FROM ro_settings WHERE code = ?', [$code]));
        $b = q1('SELECT name, phone, address FROM businesses WHERE id = ?', [$businessId]);
        q('INSERT INTO ro_settings (business_id, code, title, phone, address) VALUES (?, ?, ?, ?, ?)', [$businessId, $code, $b['name'], $b['phone'], $b['address']]);
        $s = q1('SELECT * FROM ro_settings WHERE business_id = ?', [$businessId]);
    }
    return $s;
}

function ro_public_url(string $code, string $path = ''): string
{
    return full_url('p/restaurant-ordering/' . $code . ($path !== '' ? '/' . $path : ''));
}

/** Μήνυμα για τον πελάτη ανάλογα με την κατάσταση */
function ro_customer_status(array $o): string
{
    return match ($o['status']) {
        'new'       => 'Περιμένουμε επιβεβαίωση από το κατάστημα…',
        'accepted'  => 'Η παραγγελία σας ετοιμάζεται' . ($o['eta_minutes'] ? ' · σε περίπου ' . (int) $o['eta_minutes'] . ' λεπτά' : '') . '.',
        'ready'     => 'Η παραγγελία σας είναι έτοιμη για παραλαβή!',
        'out'       => 'Η παραγγελία σας είναι στον δρόμο!',
        'completed' => 'Καλή απόλαυση! Ευχαριστούμε.',
        'rejected'  => 'Δυστυχώς η παραγγελία δεν έγινε δεκτή' . ($o['reject_reason'] ? ': ' . $o['reject_reason'] : '.'),
    };
}
