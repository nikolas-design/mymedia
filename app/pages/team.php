<?php
$biz = require_business();
$bid = (int) $biz['id'];
$me = current_user();
$canInvite = has_role('owner', 'manager');
$isOwner = has_role('owner');
$inviteLink = null;

if (is_post()) {
    $action = input('action');
    if (!$canInvite) {
        forbidden();
    }

    if ($action === 'invite' || $action === 'resend') {
        if ($action === 'resend') {
            $old = q1('SELECT * FROM invitations WHERE id = ? AND business_id = ? AND accepted_at IS NULL', [input_int('id'), $bid]);
            if (!$old) {
                not_found();
            }
            [$email, $role, $title] = [$old['email'], $old['role'], $old['job_title']];
        } else {
            $email = mb_strtolower(input('email'));
            $role = input('role');
            $title = input('job_title');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            flash('Το email δεν είναι έγκυρο.', 'error');
            redirect('team');
        }
        // Ο υπεύθυνος προσκαλεί μόνο μέλη
        $allowed = $isOwner ? ['owner', 'manager', 'member'] : ['member'];
        if (!in_array($role, $allowed, true)) {
            $role = 'member';
        }
        $already = q1('SELECT 1 FROM memberships m JOIN users u ON u.id = m.user_id
                       WHERE m.business_id = ? AND u.email = ? AND m.active = 1', [$bid, $email]);
        if ($already) {
            flash('Αυτό το άτομο είναι ήδη στην ομάδα.', 'info');
            redirect('team');
        }
        $_SESSION['invite_link'] = create_invitation($bid, $email, $role, $title, (int) $me['id']);
        flash('Η πρόσκληση στάλθηκε στο ' . $email . '.');
        redirect('team');
    }

    if ($action === 'cancel') {
        q('DELETE FROM invitations WHERE id = ? AND business_id = ? AND accepted_at IS NULL', [input_int('id'), $bid]);
        flash('Η πρόσκληση ακυρώθηκε.', 'info');
        redirect('team');
    }

    if ($action === 'update') {
        if (!$isOwner) {
            forbidden();
        }
        $m = q1('SELECT * FROM memberships WHERE id = ? AND business_id = ?', [input_int('id'), $bid]);
        if (!$m || (int) $m['user_id'] === (int) $me['id']) {
            flash('Δεν μπορείς να αλλάξεις τον δικό σου ρόλο.', 'error');
            redirect('team');
        }
        $role = in_array(input('role'), ['owner', 'manager', 'member'], true) ? input('role') : $m['role'];
        $active = input('active') === '1' ? 1 : 0;
        q('UPDATE memberships SET role = ?, job_title = ?, active = ? WHERE id = ?', [$role, input('job_title') ?: null, $active, $m['id']]);
        flash('Οι αλλαγές αποθηκεύτηκαν.');
        redirect('team');
    }
    redirect('team');
}

$inviteLink = $_SESSION['invite_link'] ?? null;
unset($_SESSION['invite_link']);

$members = qall(
    'SELECT m.*, u.name, u.email, u.last_login_at FROM memberships m JOIN users u ON u.id = m.user_id
     WHERE m.business_id = ? ORDER BY m.active DESC, m.role = \'member\', m.role = \'manager\', u.name',
    [$bid]
);
$invites = qall('SELECT * FROM invitations WHERE business_id = ? AND accepted_at IS NULL ORDER BY created_at DESC', [$bid]);
$editId = (int) ($_GET['edit'] ?? 0);

render('team', compact('members', 'invites', 'canInvite', 'isOwner', 'inviteLink', 'me', 'editId'),
    ['title' => 'Ομάδα', 'nav' => 'team']);
