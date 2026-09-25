<?php
declare(strict_types=1);

/**
 * Δείγματα δεδομένων για να φαίνεται η εφαρμογή όπως στο mockup.
 * Οι δοκιμαστικοί χρήστες έχουν τυχαίο κωδικό και δεν μπορούν να συνδεθούν.
 */
function seed_demo(int $adminId): void
{
    $tool = fn(string $slug) => (int) qval('SELECT id FROM tools WHERE slug = ?', [$slug]);
    $plan = fn(string $slug, string $name) => q1('SELECT p.* FROM plans p JOIN tools t ON t.id = p.tool_id WHERE t.slug = ? AND p.name = ?', [$slug, $name]);
    $ago = fn(string $rel) => date('Y-m-d H:i:s', strtotime($rel));

    $mkBiz = function (string $name, string $created) {
        q('INSERT INTO businesses (name, created_at) VALUES (?, ?)', [$name, $created]);
        return (int) db()->lastInsertId();
    };
    $mkUser = function (string $name, string $email, ?string $lastLogin = null) {
        q('INSERT INTO users (name, email, password_hash, last_login_at) VALUES (?, ?, ?, ?)',
            [$name, $email, password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT), $lastLogin]);
        return (int) db()->lastInsertId();
    };
    $member = fn(int $b, int $u, string $role, ?string $title = null, int $active = 1) =>
        q('INSERT INTO memberships (business_id, user_id, role, job_title, active) VALUES (?, ?, ?, ?, ?)', [$b, $u, $role, $title, $active]);
    $subscribe = function (int $b, string $slug, string $planName, string $renews, string $started) use ($tool, $plan) {
        $p = $plan($slug, $planName);
        q('INSERT INTO subscriptions (business_id, tool_id, plan_id, plan_name, price_cents, billing, started_on, renews_on) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$b, $tool($slug), $p['id'], $p['name'], $p['price_cents'], $p['period'], $started, $renews]);
    };
    $request = function (int $b, string $slug, string $planName, string $status, string $created, ?string $note = null, ?int $user = null) use ($tool, $plan) {
        $p = $plan($slug, $planName);
        q('INSERT INTO tool_requests (business_id, tool_id, plan_id, billing, note, status, user_id, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?)',
            [$b, $tool($slug), $p['id'] ?? null, 'month', $note, $status, $user, $created]);
    };

    // --- Καραγκιόζης Club: ο διαχειριστής είναι και ιδιοκτήτης, για να βλέπει και την πύλη πελάτη
    $kc = $mkBiz('Καραγκιόζης Club', $ago('-5 months'));
    q("UPDATE businesses SET legal_name = 'Καραγκιόζης Club Μ.Ι.Κ.Ε.', address = 'Αθήνα' WHERE id = ?", [$kc]);
    $member($kc, $adminId, 'owner');
    $member($kc, $mkUser('Μαρία Παπαδοπούλου', 'maria@demo.invalid', $ago('-3 hours')), 'manager');
    $member($kc, $mkUser('Γιώργος Κ.', 'giorgos@demo.invalid', $ago('-2 days')), 'member', 'Barista');
    $member($kc, $mkUser('Ελένη Λ.', 'eleni@demo.invalid', $ago('-1 day')), 'member', 'Σερβιτόρα');
    $member($kc, $mkUser('Άννα Σ.', 'anna.s@demo.invalid', $ago('-6 days')), 'member', 'Κουζίνα');
    $member($kc, $mkUser('Δημήτρης Μ.', 'dimitris@demo.invalid'), 'member', 'Parking', 0);
    q('INSERT INTO invitations (business_id, email, role, token_hash, invited_by, expires_at) VALUES (?, ?, ?, ?, ?, NOW() + INTERVAL 6 DAY)',
        [$kc, 'kostas@example.gr', 'member', hash('sha256', bin2hex(random_bytes(32))), $adminId]);

    $subscribe($kc, 'qr-boss', 'Pro', date('Y-m-25'), date('Y-m-25', strtotime('-3 months')));
    $subscribe($kc, 'tameio', 'Standard', date('Y-m-03', strtotime('+1 month')), date('Y-m-03', strtotime('-3 months')));
    $subscribe($kc, 'review-booster', 'Standard', date('Y-m-12', strtotime('+1 month')), date('Y-m-12', strtotime('-2 months')));
    $request($kc, 'orderflow', 'Standard', 'setup', $ago('-2 days'), null, $adminId);

    foreach ([['-2 months', 'paid'], ['-1 month', 'paid']] as [$rel, $status]) {
        $id = issue_invoice($kc, subscription_lines($kc), date('Y-m-25', strtotime($rel)));
        q("UPDATE invoices SET status = ?, paid_on = DATE_ADD(issued_on, INTERVAL 3 DAY) WHERE id = ?", [$status, $id]);
    }
    issue_invoice($kc, [['QR Boss · Pro (μηνιαία)', 3900]], date('Y-m-d'));

    // --- Άλλοι πελάτες, για να δείχνει κάτι η Διαχείριση
    $lim = $mkBiz('Καφέ Λήμνος', $ago('-1 month'));
    $member($lim, $mkUser('Σοφία Λ.', 'sofia@demo.invalid'), 'owner');
    $request($lim, 'orderflow', 'Standard', 'new', $ago('-2 hours'));
    $subscribe($lim, 'qr-boss', 'Standard', date('Y-m-d', strtotime('+10 days')), date('Y-m-d', strtotime('-20 days')));

    $anna = $mkBiz('Κομμωτήριο Άννα', $ago('-2 months'));
    $member($anna, $mkUser('Άννα Κ.', 'anna@demo.invalid'), 'owner');
    $request($anna, 'appointments', 'Ετήσιο', 'new', $ago('-1 day'));
    $subscribe($anna, 'websites', 'Care', date('Y-m-d', strtotime('+15 days')), date('Y-m-d', strtotime('-45 days')));

    $gian = $mkBiz('Ταβέρνα Ο Γιάννης', $ago('-3 months'));
    $gu = $mkUser('Γιάννης Π.', 'giannis@demo.invalid');
    $member($gian, $gu, 'owner');
    $request($gian, 'qr-boss', 'Pro', 'new', $ago('-5 hours'), 'θέλουμε και κλήση σερβιτόρου');
    $subscribe($gian, 'review-booster', 'Standard', date('Y-m-d', strtotime('+5 days')), date('Y-m-d', strtotime('-55 days')));
    $subscribe($gian, 'websites', 'Care', date('Y-m-d', strtotime('+5 days')), date('Y-m-d', strtotime('-55 days')));

    q('INSERT INTO tickets (business_id, user_id, subject, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
        [$gian, $gu, 'Δεν εκτυπώνεται το QR μενού', $ago('-40 minutes'), $ago('-40 minutes')]);
    q('INSERT INTO ticket_messages (ticket_id, user_id, body, created_at) VALUES (?, ?, ?, ?)',
        [db()->lastInsertId(), $gu, 'Καλησπέρα, όταν πατάω εκτύπωση βγαίνει κομμένο στη μέση. Μπορείτε να δείτε;', $ago('-40 minutes')]);
    q('INSERT INTO tickets (business_id, user_id, subject, created_at, updated_at) VALUES (?, ?, ?, ?, ?)',
        [$lim, null, 'Αλλαγή ΑΦΜ στο τιμολόγιο', $ago('-5 hours'), $ago('-5 hours')]);
    q('INSERT INTO ticket_messages (ticket_id, body, created_at) VALUES (?, ?, ?)',
        [db()->lastInsertId(), 'Θα θέλαμε τα επόμενα παραστατικά με το νέο ΑΦΜ της εταιρείας.', $ago('-5 hours')]);
}
