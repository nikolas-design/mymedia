<?php
declare(strict_types=1);

const HB_STATUS = [
    'pending'    => ['Αναμονή επιβεβαίωσης', 'orange'],
    'confirmed'  => ['Επιβεβαιωμένη', 'purple'],
    'checked_in' => ['Διαμένει', 'green'],
    'completed'  => ['Ολοκληρώθηκε', 'grey'],
    'cancelled'  => ['Ακυρώθηκε', 'grey'],
];

function hb_settings(int $businessId): array
{
    $s = q1('SELECT * FROM hb_settings WHERE business_id = ?', [$businessId]);
    if (!$s) {
        do {
            $code = short_code();
        } while (qval('SELECT 1 FROM hb_settings WHERE code = ?', [$code]));
        $b = q1('SELECT name, phone, address, billing_email FROM businesses WHERE id = ?', [$businessId]);
        q('INSERT INTO hb_settings (business_id, code, title, phone, address, email) VALUES (?, ?, ?, ?, ?, ?)', [$businessId, $code, $b['name'], $b['phone'], $b['address'], $b['billing_email']]);
        $s = q1('SELECT * FROM hb_settings WHERE business_id = ?', [$businessId]);
    }
    return $s;
}

function hb_nights(string $in, string $out): int
{
    return max(0, (int) round((strtotime($out) - strtotime($in)) / 86400));
}

/** Πόσες μονάδες ενός τύπου είναι ελεύθερες κάθε βράδυ του διαστήματος (το ελάχιστο) */
function hb_free_units(array $room, string $in, string $out, int $ignoreBooking = 0): int
{
    $free = (int) $room['units'];
    for ($d = $in; $d < $out; $d = date('Y-m-d', strtotime("$d +1 day"))) {
        $used = (int) qval("SELECT COUNT(*) FROM hb_bookings WHERE room_id = ? AND status IN ('pending','confirmed','checked_in') AND checkin <= ? AND checkout > ? AND id <> ?",
            [$room['id'], $d, $d, $ignoreBooking]);
        $blocked = (int) qval('SELECT COALESCE(SUM(units),0) FROM hb_blocks WHERE room_id = ? AND date_from <= ? AND date_to >= ?', [$room['id'], $d, $d]);
        $free = min($free, (int) $room['units'] - $used - $blocked);
    }
    return max(0, $free);
}

/** Συνολική τιμή διαμονής με τις τιμές περιόδου */
function hb_price(array $room, string $in, string $out): int
{
    $rates = qall('SELECT * FROM hb_rates WHERE room_id = ? AND date_to >= ? AND date_from < ?', [$room['id'], $in, $out]);
    $total = 0;
    for ($d = $in; $d < $out; $d = date('Y-m-d', strtotime("$d +1 day"))) {
        $p = (int) $room['base_price_cents'];
        foreach ($rates as $r) {
            if ($d >= $r['date_from'] && $d <= $r['date_to']) {
                $p = (int) $r['price_cents'];
            }
        }
        $total += $p;
    }
    return $total;
}

function hb_mail_guest(int $bookingId, string $kind): void
{
    $b = q1('SELECT b.*, r.name AS room, s.title, s.phone AS hphone, s.address, s.checkin AS cin, s.checkout AS cout, s.code, s.deposit_percent
             FROM hb_bookings b JOIN hb_rooms r ON r.id = b.room_id JOIN hb_settings s ON s.business_id = b.business_id WHERE b.id = ?', [$bookingId]);
    if (!$b) {
        return;
    }
    [$subject, $intro] = match ($kind) {
        'pending'   => ['Λάβαμε το αίτημα κράτησης', 'Λάβαμε το αίτημά σας. Θα σας επιβεβαιώσουμε σύντομα τη διαθεσιμότητα.'],
        'confirmed' => ['Η κράτησή σας επιβεβαιώθηκε', 'Η κράτησή σας επιβεβαιώθηκε. Σας περιμένουμε!'],
        'cancelled' => ['Η κράτησή σας ακυρώθηκε', 'Η κράτησή σας ακυρώθηκε.'],
        default     => ['Ενημέρωση κράτησης', 'Η κράτησή σας ενημερώθηκε.'],
    };
    send_mail($b['email'], $subject . ' · ' . $b['title'],
        "Αγαπητέ/ή {$b['name']},\n\n$intro\n\n{$b['room']} · {$b['guests']} άτομα\nΆφιξη: " . date_gr($b['checkin']) . " (από {$b['cin']})\nΑναχώρηση: " . date_gr($b['checkout']) . " (έως {$b['cout']})\n"
        . 'Σύνολο: ' . money($b['total_cents']) . "\n" . ($b['address'] ? "\n{$b['address']}" : '') . ($b['hphone'] ? "\nΤηλ. {$b['hphone']}" : '')
        . "\n\nΗ κράτησή σας: " . full_url('p/hotel-booking/' . $b['code'] . '/b/' . $b['token']) . "\n\n{$b['title']}");
}
