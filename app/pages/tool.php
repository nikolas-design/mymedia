<?php
$biz = require_business();
$bid = (int) $biz['id'];
$tool = q1("SELECT * FROM tools WHERE slug = ? AND status <> 'hidden'", [$params[0]]);
if (!$tool) {
    not_found();
}
$plans = qall('SELECT * FROM plans WHERE tool_id = ? AND active = 1 ORDER BY sort', [$tool['id']]);
$subscription = active_subscription($bid, (int) $tool['id']);
$request = q1("SELECT * FROM tool_requests WHERE business_id = ? AND tool_id = ? AND status IN ('new','setup') ORDER BY id DESC LIMIT 1",
    [$bid, $tool['id']]);
$canRequest = has_role('owner', 'manager');

if (is_post()) {
    if (!$canRequest) {
        forbidden();
    }
    if ($request) {
        flash('Υπάρχει ήδη αίτημα σε εξέλιξη για αυτό το εργαλείο.', 'info');
        redirect('tools/' . $tool['slug']);
    }
    $planId = input_int('plan_id');
    $plan = null;
    foreach ($plans as $p) {
        if ((int) $p['id'] === $planId) {
            $plan = $p;
        }
    }
    if ($plans && !$plan && $tool['status'] === 'available') {
        flash('Διάλεξε πλάνο.', 'error');
        redirect('tools/' . $tool['slug']);
    }
    $billing = input('billing') === 'year' ? 'year' : 'month';
    q('INSERT INTO tool_requests (business_id, tool_id, plan_id, billing, note, user_id) VALUES (?, ?, ?, ?, ?, ?)',
        [$bid, $tool['id'], $plan['id'] ?? null, $billing, input('note') ?: null, current_user()['id']]);
    notify(null, $biz['name'] . ' → ' . $tool['name'] . ($plan ? ' ' . $plan['name'] : ' (ενδιαφέρον)'), 'admin/requests');
    flash($tool['status'] === 'soon'
        ? 'Σημειώσαμε το ενδιαφέρον σου. Θα σε ενημερώσουμε μόλις είναι διαθέσιμο.'
        : 'Το αίτημα στάλθηκε. Θα επικοινωνήσουμε μαζί σου για την ενεργοποίηση.');
    redirect($tool['status'] === 'soon' ? 'tools/' . $tool['slug'] : 'dashboard');
}

$number = (int) qval("SELECT COUNT(*) FROM tools WHERE status <> 'hidden' AND sort <= ?", [$tool['sort']]);
render('tool', compact('tool', 'plans', 'subscription', 'request', 'canRequest', 'number'),
    ['title' => $tool['name'], 'nav' => 'tools']);
