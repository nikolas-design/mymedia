<?php
declare(strict_types=1);

/* ---------- Ειδοποιήσεις ---------- */

/** $businessId null = ειδοποίηση για τους admin */
function notify(?int $businessId, string $title, ?string $link = null): void
{
    q('INSERT INTO notifications (business_id, title, link) VALUES (?, ?, ?)', [$businessId, $title, $link]);
}

/* ---------- Αιτήματα ενεργοποίησης ---------- */

const REQUEST_STATUS = [
    'new'      => ['Υποβλήθηκε', 'orange'],
    'setup'    => ['Σε ρύθμιση', 'purple'],
    'done'     => ['Ενεργοποιήθηκε', 'green'],
    'rejected' => ['Απορρίφθηκε', 'grey'],
];

function request_pill(string $status): string
{
    [$label, $tone] = REQUEST_STATUS[$status] ?? [$status, 'grey'];
    return pill($label, $tone);
}

/** Τα 3 βήματα προόδου ενός αιτήματος: [ετικέτα, κατάσταση] με κατάσταση done|progress|pending */
function request_steps(string $status): array
{
    $s = fn(bool $done, bool $progress) => $done ? 'done' : ($progress ? 'progress' : 'pending');
    return [
        ['Έγκριση αιτήματος',     $s(in_array($status, ['setup', 'done'], true), $status === 'new')],
        ['Ρύθμιση λογαριασμού',   $s($status === 'done', $status === 'setup')],
        ['Ενεργοποίηση εργαλείου', $s($status === 'done', false)],
    ];
}

function step_pill(string $state): string
{
    return match ($state) {
        'done'     => pill('Έτοιμο', 'green'),
        'progress' => pill('Σε εξέλιξη', 'purple'),
        default    => pill('Εκκρεμεί', 'grey'),
    };
}

/**
 * Ενεργοποιεί συνδρομή από αίτημα.
 * Η ετήσια χρέωση μηνιαίου πλάνου κοστίζει 10 μήνες (2 μήνες δώρο).
 */
function activate_request(array $req): void
{
    $plan = $req['plan_id'] ? q1('SELECT * FROM plans WHERE id = ?', [$req['plan_id']]) : null;
    if (!$plan) {
        $plan = q1('SELECT * FROM plans WHERE tool_id = ? AND active = 1 ORDER BY sort LIMIT 1', [$req['tool_id']]);
    }
    if (!$plan) {
        throw new RuntimeException('Το εργαλείο δεν έχει πλάνο τιμών.');
    }
    [$price, $billing] = plan_price($plan, $req['billing']);

    $existing = active_subscription((int) $req['business_id'], (int) $req['tool_id']);
    if ($existing) {
        // Αλλαγή πλάνου στην υπάρχουσα συνδρομή
        q('UPDATE subscriptions SET plan_id = ?, plan_name = ?, price_cents = ?, billing = ? WHERE id = ?',
            [$plan['id'], $plan['name'], $price, $billing, $existing['id']]);
    } else {
        $renews = date('Y-m-d', strtotime($billing === 'year' ? '+1 year' : '+1 month'));
        q('INSERT INTO subscriptions (business_id, tool_id, plan_id, plan_name, price_cents, billing, started_on, renews_on)
           VALUES (?, ?, ?, ?, ?, ?, CURDATE(), ?)',
            [$req['business_id'], $req['tool_id'], $plan['id'], $plan['name'], $price, $billing, $renews]);
    }
    q("UPDATE tool_requests SET status = 'done', updated_at = NOW() WHERE id = ?", [$req['id']]);
    $tool = q1('SELECT name, slug FROM tools WHERE id = ?', [$req['tool_id']]);
    notify((int) $req['business_id'], 'Το ' . $tool['name'] . ' ενεργοποιήθηκε', 'tools/' . $tool['slug']);
}

/** [τιμή σε λεπτά, 'month'|'year'] για ένα πλάνο και την επιλογή χρέωσης */
function plan_price(array $plan, string $billing): array
{
    if ($plan['period'] === 'year') {
        return [(int) $plan['price_cents'], 'year'];
    }
    if ($billing === 'year') {
        return [(int) $plan['price_cents'] * 10, 'year'];
    }
    return [(int) $plan['price_cents'], 'month'];
}

/* ---------- Προσκλήσεις ---------- */

/** Δημιουργεί πρόσκληση και επιστρέφει τον σύνδεσμο. Στέλνει και email. */
function create_invitation(int $businessId, string $email, string $role, ?string $jobTitle, int $invitedBy): string
{
    $token = bin2hex(random_bytes(32));
    q('DELETE FROM invitations WHERE business_id = ? AND email = ? AND accepted_at IS NULL', [$businessId, $email]);
    q('INSERT INTO invitations (business_id, email, role, job_title, token_hash, invited_by, expires_at)
       VALUES (?, ?, ?, ?, ?, ?, NOW() + INTERVAL 7 DAY)',
        [$businessId, $email, $role, $jobTitle ?: null, hash('sha256', $token), $invitedBy]);

    $link = full_url('invite/' . $token);
    $biz = q1('SELECT name FROM businesses WHERE id = ?', [$businessId]);
    $app = $GLOBALS['config']['app_name'] ?? 'MyMedia';
    send_mail($email, 'Πρόσκληση στην ομάδα: ' . $biz['name'],
        "Γεια σου,\n\nΣε προσκάλεσαν στην ομάδα της επιχείρησης «{$biz['name']}» στο $app.\n\n"
        . "Άνοιξε τον σύνδεσμο για να μπεις (ισχύει 7 ημέρες):\n$link\n");
    return $link;
}

/* ---------- Παραστατικά ---------- */

const INVOICE_STATUS = [
    'issued' => ['Εκδόθηκε', 'orange'],
    'paid'   => ['Πληρωμένο', 'green'],
    'void'   => ['Ακυρώθηκε', 'grey'],
];

function invoice_pill(string $status, ?string $dueOn = null): string
{
    if ($status === 'issued' && $dueOn && $dueOn < date('Y-m-d')) {
        return pill('Ληξιπρόθεσμο', 'red');
    }
    [$label, $tone] = INVOICE_STATUS[$status] ?? [$status, 'grey'];
    return pill($label, $tone);
}

function next_invoice_number(): string
{
    $prefix = 'MM-' . date('Y') . '-';
    $last = qval('SELECT number FROM invoices WHERE number LIKE ? ORDER BY number DESC LIMIT 1', [$prefix . '%']);
    $n = $last ? ((int) substr($last, strlen($prefix))) + 1 : 1;
    return $prefix . str_pad((string) $n, 4, '0', STR_PAD_LEFT);
}

/**
 * Εκδίδει παραστατικό. $lines: [[περιγραφή, καθαρό ποσό σε λεπτά], ...]
 * Επιστρέφει το id.
 */
function issue_invoice(int $businessId, array $lines, ?string $issuedOn = null): int
{
    $issuedOn = $issuedOn ?: date('Y-m-d');
    $net = array_sum(array_column($lines, 1));
    $vat = (int) round($net * VAT_RATE / 100);
    $due = date('Y-m-d', strtotime($issuedOn . ' +' . (int) setting('invoice_due_days', '7') . ' days'));

    db()->beginTransaction();
    try {
        q('INSERT INTO invoices (business_id, number, issued_on, due_on, net_cents, vat_rate, vat_cents, total_cents)
           VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$businessId, next_invoice_number(), $issuedOn, $due, $net, VAT_RATE, $vat, $net + $vat]);
        $id = (int) db()->lastInsertId();
        foreach ($lines as [$desc, $amount]) {
            q('INSERT INTO invoice_lines (invoice_id, description, amount_cents) VALUES (?, ?, ?)', [$id, $desc, $amount]);
        }
        db()->commit();
    } catch (Throwable $e) {
        db()->rollBack();
        throw $e;
    }
    notify($businessId, 'Νέο παραστατικό ' . money($net + $vat), 'invoices/' . $id);
    return $id;
}

/** Γραμμές παραστατικού από τις ενεργές συνδρομές μιας επιχείρησης */
function subscription_lines(int $businessId): array
{
    $lines = [];
    foreach (business_subscriptions($businessId) as $s) {
        $lines[] = [$s['tool_name'] . ' · ' . $s['plan_name'] . ($s['billing'] === 'year' ? ' (ετήσια)' : ' (μηνιαία)'), (int) $s['price_cents']];
    }
    return $lines;
}

/* ---------- Ερωτήματα που χρησιμοποιούνται σε πολλές σελίδες ---------- */

function business_subscriptions(int $businessId): array
{
    return qall(
        "SELECT s.*, t.name AS tool_name, t.slug, t.icon, t.color
         FROM subscriptions s JOIN tools t ON t.id = s.tool_id
         WHERE s.business_id = ? AND s.status = 'active' ORDER BY t.sort",
        [$businessId]
    );
}

/** Τι βλέπει μια επιχείρηση για κάθε εργαλείο: active | requested | available | soon */
function tool_states(int $businessId): array
{
    $states = [];
    foreach (qall("SELECT tool_id FROM subscriptions WHERE business_id = ? AND status = 'active'", [$businessId]) as $r) {
        $states[(int) $r['tool_id']] = 'active';
    }
    foreach (qall("SELECT tool_id FROM tool_requests WHERE business_id = ? AND status IN ('new','setup')", [$businessId]) as $r) {
        $states[(int) $r['tool_id']] ??= 'requested';
    }
    return $states;
}

function tool_state_label(string $state): string
{
    return match ($state) {
        'active'    => 'Ενεργό',
        'requested' => 'Αίτημα σε εξέλιξη',
        'soon'      => 'Σύντομα',
        default     => 'Διαθέσιμο τώρα',
    };
}

/* ---------- Υποστήριξη ---------- */

function ticket_pill(string $status): string
{
    return match ($status) {
        'open'     => pill('Ανοιχτό', 'orange'),
        'answered' => pill('Απαντήθηκε', 'purple'),
        default    => pill('Κλειστό', 'grey'),
    };
}
