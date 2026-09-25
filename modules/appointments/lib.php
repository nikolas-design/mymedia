<?php
declare(strict_types=1);

const AP_DAYS = [1 => 'Δευτέρα', 2 => 'Τρίτη', 3 => 'Τετάρτη', 4 => 'Πέμπτη', 5 => 'Παρασκευή', 6 => 'Σάββατο', 7 => 'Κυριακή'];
const AP_STATUS = [
    'pending'   => ['Αναμονή επιβεβαίωσης', 'orange'],
    'confirmed' => ['Επιβεβαιωμένο', 'purple'],
    'done'      => ['Ολοκληρώθηκε', 'green'],
    'cancelled' => ['Ακυρώθηκε', 'grey'],
    'noshow'    => ['Δεν ήρθε', 'red'],
];

function ap_settings(int $businessId): array
{
    $s = q1('SELECT * FROM ap_settings WHERE business_id = ?', [$businessId]);
    if (!$s) {
        do {
            $code = short_code();
        } while (qval('SELECT 1 FROM ap_settings WHERE code = ?', [$code]));
        $b = q1('SELECT name, phone, address FROM businesses WHERE id = ?', [$businessId]);
        q('INSERT INTO ap_settings (business_id, code, title, phone, address) VALUES (?, ?, ?, ?, ?)', [$businessId, $code, $b['name'], $b['phone'], $b['address']]);
        $s = q1('SELECT * FROM ap_settings WHERE business_id = ?', [$businessId]);
    }
    return $s;
}

function ap_public_url(string $code, string $path = ''): string
{
    return full_url('p/appointments/' . $code . ($path !== '' ? '/' . $path : ''));
}

function ap_dt(string $dt): string
{
    $t = strtotime($dt);
    return AP_DAYS[(int) date('N', $t)] . ' ' . date('d/m', $t) . ' στις ' . date('H:i', $t);
}

/** Συνεργάτες που κάνουν μια υπηρεσία */
function ap_staff_for(int $businessId, int $serviceId): array
{
    return qall('SELECT s.* FROM ap_staff s JOIN ap_staff_services x ON x.staff_id = s.id
                 WHERE s.business_id = ? AND s.active = 1 AND x.service_id = ? ORDER BY s.name', [$businessId, $serviceId]);
}

/**
 * Ελεύθερες ώρες για συνεργάτη, ημέρα και διάρκεια: ['H:i', ...]
 * $ignoreBooking: για μεταφορά ραντεβού (να μη «συγκρούεται» με τον εαυτό του)
 */
function ap_free_slots(array $settings, int $staffId, string $day, int $duration, bool $public = true, int $ignoreBooking = 0): array
{
    $weekday = (int) date('N', strtotime($day));
    if (qval('SELECT 1 FROM ap_timeoff WHERE staff_id = ? AND day_from <= ? AND day_to >= ?', [$staffId, $day, $day])) {
        return [];
    }
    $busy = [];
    foreach (qall("SELECT starts_at, ends_at FROM ap_bookings WHERE staff_id = ? AND status IN ('pending','confirmed','done')
                   AND starts_at < ? AND ends_at > ? AND id <> ?", [$staffId, "$day 23:59:59", "$day 00:00:00", $ignoreBooking]) as $b) {
        $busy[] = [strtotime($b['starts_at']), strtotime($b['ends_at'])];
    }
    $step = max(5, (int) $settings['slot_minutes']) * 60;
    // Online: με την ελάχιστη προειδοποίηση. Από την ομάδα: από τώρα και μετά.
    $earliest = time() + ($public ? (int) $settings['min_notice_hours'] * 3600 : 0);
    $slots = [];
    foreach (qall('SELECT * FROM ap_hours WHERE staff_id = ? AND weekday = ? ORDER BY start_time', [$staffId, $weekday]) as $h) {
        $from = strtotime("$day {$h['start_time']}");
        $to = strtotime("$day {$h['end_time']}");
        for ($t = $from; $t + $duration * 60 <= $to; $t += $step) {
            if ($t < $earliest) {
                continue;
            }
            $end = $t + $duration * 60;
            foreach ($busy as [$bs, $be]) {
                if ($t < $be && $end > $bs) {
                    continue 2;
                }
            }
            $slots[] = date('H:i', $t);
        }
    }
    return array_values(array_unique($slots));
}

/** Βρίσκει ή δημιουργεί πελάτη με βάση το τηλέφωνο */
function ap_customer(int $businessId, string $name, string $phone, ?string $email): int
{
    $phone = preg_replace('/[^0-9+]/', '', $phone);
    $c = q1('SELECT * FROM ap_customers WHERE business_id = ? AND phone = ?', [$businessId, $phone]);
    if ($c) {
        q('UPDATE ap_customers SET name = ?, email = COALESCE(?, email) WHERE id = ?', [$name, $email ?: null, $c['id']]);
        return (int) $c['id'];
    }
    q('INSERT INTO ap_customers (business_id, name, phone, email) VALUES (?, ?, ?, ?)', [$businessId, $name, $phone, $email ?: null]);
    return (int) db()->lastInsertId();
}

/**
 * Δημιουργεί κράτηση αφού ξαναελέγξει ότι η ώρα είναι ελεύθερη (μέσα σε κλείδωμα).
 * Επιστρέφει το id ή null αν η ώρα πιάστηκε στο μεταξύ.
 */
function ap_book(array $settings, array $service, int $staffId, string $day, string $time, int $customerId, string $source, ?string $notes, string $status): ?int
{
    $lock = 'ap_staff_' . $staffId;
    qval('SELECT GET_LOCK(?, 5)', [$lock]);
    try {
        if (!in_array($time, ap_free_slots($settings, $staffId, $day, (int) $service['duration_min'], $source === 'online'), true)) {
            return null;
        }
        $start = "$day $time:00";
        $end = date('Y-m-d H:i:s', strtotime($start) + (int) $service['duration_min'] * 60);
        q('INSERT INTO ap_bookings (business_id, staff_id, service_id, customer_id, service_name, starts_at, ends_at, price_cents, status, source, notes, token)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)', [
            $settings['business_id'], $staffId, $service['id'], $customerId, $service['name'], $start, $end, $service['price_cents'],
            $status, $source, $notes, bin2hex(random_bytes(16)),
        ]);
        return (int) db()->lastInsertId();
    } finally {
        qval('SELECT RELEASE_LOCK(?)', [$lock]);
    }
}

/** Email επιβεβαίωσης/ενημέρωσης στον πελάτη */
function ap_mail_customer(int $bookingId, string $kind): void
{
    $b = q1('SELECT b.*, c.name, c.email, s.name AS staff, st.code, st.title, st.phone AS biz_phone, st.address
             FROM ap_bookings b JOIN ap_customers c ON c.id = b.customer_id JOIN ap_staff s ON s.id = b.staff_id
             JOIN ap_settings st ON st.business_id = b.business_id WHERE b.id = ?', [$bookingId]);
    if (!$b || !$b['email']) {
        return;
    }
    $when = ap_dt($b['starts_at']);
    $manage = ap_public_url($b['code'], 'b/' . $b['token']);
    [$subject, $intro] = match ($kind) {
        'pending'   => ['Λάβαμε το αίτημα ραντεβού', 'Λάβαμε το αίτημά σας και θα σας επιβεβαιώσουμε σύντομα.'],
        'confirmed' => ['Το ραντεβού σας επιβεβαιώθηκε', 'Το ραντεβού σας επιβεβαιώθηκε.'],
        'reminder'  => ['Υπενθύμιση ραντεβού', 'Σας υπενθυμίζουμε το ραντεβού σας.'],
        'cancelled' => ['Το ραντεβού ακυρώθηκε', 'Το ραντεβού σας ακυρώθηκε.'],
        default     => ['Ενημέρωση ραντεβού', 'Το ραντεβού σας ενημερώθηκε.'],
    };
    send_mail($b['email'], $subject . ' · ' . $b['title'],
        "Γεια σας {$b['name']},\n\n$intro\n\n{$b['service_name']} με {$b['staff']}\n$when\n"
        . ($b['address'] ? "{$b['address']}\n" : '') . ($b['biz_phone'] ? "Τηλ. {$b['biz_phone']}\n" : '')
        . ($kind !== 'cancelled' ? "\nΑλλαγή ή ακύρωση: $manage\n" : '') . "\n{$b['title']}");
}
