<?php
declare(strict_types=1);

// Review Booster: κοινά

/** Όριο σημείων ανά πλάνο */
function rb_location_limit(array $subscription): int
{
    return stripos((string) $subscription['plan_name'], 'pro') !== false ? 3 : 1;
}

function rb_public_url(string $code): string
{
    return full_url('r/' . $code);
}

/** Σύνοψη αξιολογήσεων για διάστημα: count, avg, positive, google, feedback, dist[5..1] */
function rb_summary(int $businessId, string $from, ?string $to = null, ?int $locationId = null): array
{
    $where = 'business_id = ? AND created_at >= ?';
    $args = [$businessId, $from];
    if ($to !== null) {
        $where .= ' AND created_at < ?';
        $args[] = $to;
    }
    if ($locationId) {
        $where .= ' AND location_id = ?';
        $args[] = $locationId;
    }
    $r = q1("SELECT COUNT(*) AS n, AVG(stars) AS avg, SUM(stars >= 4) AS pos, SUM(went_google) AS google FROM rb_ratings WHERE $where", $args);
    $dist = [5 => 0, 4 => 0, 3 => 0, 2 => 0, 1 => 0];
    foreach (qall("SELECT stars, COUNT(*) AS n FROM rb_ratings WHERE $where GROUP BY stars", $args) as $d) {
        $dist[(int) $d['stars']] = (int) $d['n'];
    }
    $fb = (int) qval("SELECT COUNT(*) FROM rb_feedback WHERE $where", $args);
    return [
        'count' => (int) $r['n'],
        'avg' => $r['n'] ? round((float) $r['avg'], 1) : null,
        'positive' => $r['n'] ? (int) round($r['pos'] / $r['n'] * 100) : null,
        'google' => (int) $r['google'],
        'feedback' => $fb,
        'dist' => $dist,
    ];
}

function rb_stars(int $n): string
{
    return '<span style="color:#f5b301;letter-spacing:1px">' . str_repeat('★', $n) . '</span><span style="color:#d9dae1;letter-spacing:1px">' . str_repeat('★', 5 - $n) . '</span>';
}

const RB_DEFAULT_TEMPLATES = [
    ['Ευχαριστώ (θετική)', 'positive', "Σας ευχαριστούμε πολύ {όνομα} για τα καλά σας λόγια! Χαιρόμαστε που περάσατε όμορφα και σας περιμένουμε ξανά σύντομα στο {επιχείρηση}."],
    ['Ευχαριστώ (σύντομη)', 'positive', "Ευχαριστούμε {όνομα}! Να είστε πάντα καλά, σας περιμένουμε ξανά."],
    ['Ουδέτερη', 'neutral', "Ευχαριστούμε {όνομα} για την αξιολόγηση. Θα θέλαμε να μάθουμε τι θα μπορούσαμε να κάνουμε καλύτερα, για να είναι η επόμενη επίσκεψή σας πεντάστερη."],
    ['Αρνητική', 'negative', "{όνομα}, λυπούμαστε πολύ που η εμπειρία σας δεν ήταν αυτή που περιμένατε. Θα θέλαμε να το συζητήσουμε και να το διορθώσουμε. Επικοινωνήστε μαζί μας στο {τηλέφωνο}."],
];
