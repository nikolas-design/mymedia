<?php
declare(strict_types=1);

const SH_DAYS = ['Δευτέρα', 'Τρίτη', 'Τετάρτη', 'Πέμπτη', 'Παρασκευή', 'Σάββατο', 'Κυριακή'];
const SH_KIND = ['off' => 'Ρεπό', 'leave' => 'Άδεια', 'swap' => 'Αλλαγή βάρδιας'];

/** Δευτέρα της εβδομάδας μιας ημερομηνίας */
function sh_monday(string $date): string
{
    $t = strtotime($date);
    return date('Y-m-d', strtotime('-' . (((int) date('N', $t)) - 1) . ' days', $t));
}

function sh_week_days(string $monday): array
{
    $out = [];
    for ($i = 0; $i < 7; $i++) {
        $out[] = date('Y-m-d', strtotime("$monday +$i days"));
    }
    return $out;
}

/** Ώρες βάρδιας (και όταν τελειώνει μετά τα μεσάνυχτα) */
function sh_hours(string $start, string $end): float
{
    $s = strtotime("2000-01-01 $start");
    $e = strtotime("2000-01-01 $end");
    if ($e <= $s) {
        $e += 86400;
    }
    return round(($e - $s) / 3600, 2);
}

function sh_time(string $t): string
{
    return substr($t, 0, 5);
}

function sh_settings(int $businessId): array
{
    $s = q1('SELECT * FROM sh_settings WHERE business_id = ?', [$businessId]);
    if (!$s) {
        q('INSERT INTO sh_settings (business_id, share_token) VALUES (?, ?)', [$businessId, bin2hex(random_bytes(12))]);
        $s = q1('SELECT * FROM sh_settings WHERE business_id = ?', [$businessId]);
    }
    return $s;
}

/** Πρόγραμμα εβδομάδας: [person_id][day] => [βάρδιες] */
function sh_week(int $businessId, string $monday): array
{
    $grid = [];
    foreach (qall('SELECT * FROM sh_shifts WHERE business_id = ? AND day >= ? AND day <= ? ORDER BY start_time',
        [$businessId, $monday, date('Y-m-d', strtotime("$monday +6 days"))]) as $s) {
        $grid[(int) $s['person_id']][$s['day']][] = $s;
    }
    return $grid;
}

/** Εγκεκριμένες άδειες/ρεπό της εβδομάδας: [person_id][day] => kind */
function sh_absences(int $businessId, string $monday): array
{
    $sunday = date('Y-m-d', strtotime("$monday +6 days"));
    $out = [];
    foreach (qall("SELECT * FROM sh_requests WHERE business_id = ? AND status = 'approved' AND kind IN ('off','leave') AND day_to >= ? AND day_from <= ?",
        [$businessId, $monday, $sunday]) as $r) {
        foreach (sh_week_days($monday) as $d) {
            if ($d >= $r['day_from'] && $d <= $r['day_to']) {
                $out[(int) $r['person_id']][$d] = $r['kind'];
            }
        }
    }
    return $out;
}
