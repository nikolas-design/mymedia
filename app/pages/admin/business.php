<?php
$me = require_admin();
$b = q1('SELECT * FROM businesses WHERE id = ?', [(int) $params[0]]);
if (!$b) {
    not_found();
}
$bid = (int) $b['id'];
$here = 'admin/businesses/' . $bid;

if (is_post()) {
    switch (input('action')) {
        case 'info':
            $name = input('name');
            if ($name === '') {
                flash('Το όνομα είναι υποχρεωτικό.', 'error');
                break;
            }
            q('UPDATE businesses SET name = ?, legal_name = ?, vat_number = ?, tax_office = ?, address = ?, phone = ?, billing_email = ? WHERE id = ?', [
                $name, input('legal_name') ?: null, input('vat_number') ?: null, input('tax_office') ?: null,
                input('address') ?: null, input('phone') ?: null, input('billing_email') ?: null, $bid,
            ]);
            flash('Τα στοιχεία αποθηκεύτηκαν.');
            break;

        case 'invite':
            $email = mb_strtolower(input('email'));
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                flash('Το email δεν είναι έγκυρο.', 'error');
                break;
            }
            $role = in_array(input('role'), ['owner', 'manager', 'member'], true) ? input('role') : 'member';
            $_SESSION['invite_link'] = create_invitation($bid, $email, $role, null, (int) $me['id']);
            flash('Η πρόσκληση στάλθηκε.');
            break;

        case 'add_sub':
            $plan = q1('SELECT p.*, t.id AS tid FROM plans p JOIN tools t ON t.id = p.tool_id WHERE p.id = ?', [input_int('plan_id')]);
            if (!$plan) {
                flash('Διάλεξε πλάνο.', 'error');
                break;
            }
            if (active_subscription($bid, (int) $plan['tid'])) {
                flash('Υπάρχει ήδη ενεργή συνδρομή σε αυτό το εργαλείο.', 'error');
                break;
            }
            [$price, $billing] = plan_price($plan, input('billing'));
            $start = input('started_on') ?: date('Y-m-d');
            $renews = date('Y-m-d', strtotime($start . ($billing === 'year' ? ' +1 year' : ' +1 month')));
            q('INSERT INTO subscriptions (business_id, tool_id, plan_id, plan_name, price_cents, billing, started_on, renews_on)
               VALUES (?, ?, ?, ?, ?, ?, ?, ?)', [$bid, $plan['tid'], $plan['id'], $plan['name'], $price, $billing, $start, $renews]);
            $tool = q1('SELECT name, slug FROM tools WHERE id = ?', [$plan['tid']]);
            notify($bid, 'Το ' . $tool['name'] . ' ενεργοποιήθηκε', 'tools/' . $tool['slug']);
            q("UPDATE tool_requests SET status = 'done', updated_at = NOW() WHERE business_id = ? AND tool_id = ? AND status IN ('new','setup')", [$bid, $plan['tid']]);
            flash('Η συνδρομή προστέθηκε.');
            break;

        case 'edit_sub':
            $s = q1('SELECT * FROM subscriptions WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
            if (!$s) {
                break;
            }
            if (input('cancel') === '1') {
                q("UPDATE subscriptions SET status = 'cancelled', cancelled_on = CURDATE() WHERE id = ?", [$s['id']]);
                flash('Η συνδρομή ακυρώθηκε.', 'info');
                break;
            }
            q('UPDATE subscriptions SET price_cents = ?, renews_on = ? WHERE id = ?',
                [parse_money(input('price')), input('renews_on') ?: $s['renews_on'], $s['id']]);
            flash('Η συνδρομή ενημερώθηκε.');
            break;

        case 'invoice':
            $lines = [];
            $renewIds = array_map('intval', (array) ($_POST['subs'] ?? []));
            foreach (business_subscriptions($bid) as $s) {
                if (in_array((int) $s['id'], $renewIds, true)) {
                    $next = date('Y-m-d', strtotime($s['renews_on'] . ($s['billing'] === 'year' ? ' +1 year' : ' +1 month')));
                    $lines[] = [$s['tool_name'] . ' · ' . $s['plan_name'] . ($s['billing'] === 'year' ? ' (ετήσια)' : ' (μηνιαία)')
                        . ' · περίοδος ' . date_gr($s['renews_on']) . ' – ' . date_gr($next), (int) $s['price_cents']];
                }
            }
            if (input('extra_desc') !== '' && parse_money(input('extra_amount')) > 0) {
                $lines[] = [input('extra_desc'), parse_money(input('extra_amount'))];
            }
            if (!$lines) {
                flash('Διάλεξε τουλάχιστον μία γραμμή.', 'error');
                break;
            }
            $id = issue_invoice($bid, $lines, input('issued_on') ?: null);
            // Οι επιλεγμένες συνδρομές ανανεώνονται για την επόμενη περίοδο
            foreach ($renewIds as $sid) {
                q("UPDATE subscriptions SET renews_on = DATE_ADD(renews_on, INTERVAL IF(billing = 'year', 12, 1) MONTH)
                   WHERE id = ? AND business_id = ? AND status = 'active'", [$sid, $bid]);
            }
            flash('Το παραστατικό εκδόθηκε.');
            redirect('invoices/' . $id);

        case 'delete':
            if (input('confirm_name') !== $b['name']) {
                flash('Για διαγραφή γράψε ακριβώς το όνομα της επιχείρησης.', 'error');
                break;
            }
            q('DELETE FROM businesses WHERE id = ?', [$bid]);
            // Χρήστες που δεν ανήκουν πια πουθενά (και δεν είναι admin) δεν μπορούν να κάνουν τίποτα: σβήνονται
            q('DELETE FROM users WHERE is_admin = 0 AND id NOT IN (SELECT user_id FROM memberships)');
            flash('Η επιχείρηση «' . $b['name'] . '» διαγράφηκε.', 'info');
            redirect('admin/businesses');

        case 'member':
            $m = q1('SELECT * FROM memberships WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
            if ($m) {
                $role = in_array(input('role'), ['owner', 'manager', 'member'], true) ? input('role') : $m['role'];
                q('UPDATE memberships SET role = ?, active = ? WHERE id = ?', [$role, input('active') === '1' ? 1 : 0, $m['id']]);
                flash('Το μέλος ενημερώθηκε.');
            }
            break;
    }
    redirect($here);
}

$inviteLink = $_SESSION['invite_link'] ?? null;
unset($_SESSION['invite_link']);

$subs = business_subscriptions($bid);
$members = qall('SELECT m.*, u.name, u.email, u.last_login_at FROM memberships m JOIN users u ON u.id = m.user_id
                 WHERE m.business_id = ? ORDER BY m.active DESC, u.name', [$bid]);
$invites = qall('SELECT * FROM invitations WHERE business_id = ? AND accepted_at IS NULL ORDER BY id DESC', [$bid]);
$invoices = qall('SELECT * FROM invoices WHERE business_id = ? ORDER BY issued_on DESC, id DESC', [$bid]);
$requests = qall("SELECT r.*, t.name AS tool_name FROM tool_requests r JOIN tools t ON t.id = r.tool_id
                  WHERE r.business_id = ? ORDER BY r.id DESC LIMIT 10", [$bid]);
$plans = qall("SELECT p.*, t.name AS tool_name FROM plans p JOIN tools t ON t.id = p.tool_id
               WHERE p.active = 1 ORDER BY t.sort, p.sort");

render('admin/business', compact('b', 'subs', 'members', 'invites', 'invoices', 'requests', 'plans', 'inviteLink'),
    ['title' => $b['name'], 'nav' => 'admin-businesses', 'area' => 'admin']);
