<?php
declare(strict_types=1);

const WS_STATUS = [
    'new'         => ['Νέο', 'orange'],
    'in_progress' => ['Σε εξέλιξη', 'purple'],
    'done'        => ['Ολοκληρώθηκε', 'green'],
    'rejected'    => ['Εκτός πακέτου', 'grey'],
];

function ws_site(int $businessId): array
{
    $s = q1('SELECT * FROM ws_sites WHERE business_id = ?', [$businessId]);
    if (!$s) {
        q('INSERT INTO ws_sites (business_id) VALUES (?)', [$businessId]);
        $s = q1('SELECT * FROM ws_sites WHERE business_id = ?', [$businessId]);
    }
    return $s;
}

/** Ημερομηνία λήξης με χρώμα ανάλογα με το πόσο κοντά είναι */
function ws_expiry(?string $date): string
{
    if (!$date) {
        return '<span class="muted">—</span>';
    }
    $days = (int) floor((strtotime($date) - strtotime('today')) / 86400);
    $tone = $days < 0 ? 'red' : ($days <= 30 ? 'orange' : 'green');
    return e(date_gr($date)) . ' ' . pill($days < 0 ? 'Έληξε' : ($days <= 30 ? "σε $days ημ." : 'OK'), $tone);
}

function ws_month_used(int $businessId): int
{
    return (int) qval("SELECT COUNT(*) FROM ws_requests WHERE business_id = ? AND status <> 'rejected' AND created_at >= DATE_FORMAT(CURDATE(), '%Y-%m-01')", [$businessId]);
}
