<?php
require_admin();

if (is_post()) {
    $r = q1('SELECT * FROM tool_requests WHERE id = ?', [input_int('id')]);
    if (!$r) {
        not_found();
    }
    $do = input('do');
    if ($do === 'activate') {
        try {
            activate_request($r);
            flash('Η συνδρομή ενεργοποιήθηκε.');
        } catch (RuntimeException $e) {
            flash($e->getMessage(), 'error');
        }
    } elseif ($do === 'setup') {
        q("UPDATE tool_requests SET status = 'setup', updated_at = NOW() WHERE id = ?", [$r['id']]);
        $tool = q1('SELECT name, slug FROM tools WHERE id = ?', [$r['tool_id']]);
        notify((int) $r['business_id'], 'Το αίτημα για ' . $tool['name'] . ' εγκρίθηκε: ρυθμίζουμε τον λογαριασμό σου', 'tools/' . $tool['slug']);
        flash('Το αίτημα πέρασε σε ρύθμιση.');
    } elseif ($do === 'reject') {
        q("UPDATE tool_requests SET status = 'rejected', updated_at = NOW() WHERE id = ?", [$r['id']]);
        flash('Το αίτημα απορρίφθηκε.', 'info');
    }
    $back = $_SERVER['HTTP_REFERER'] ?? '';
    redirect(str_contains($back, '/admin/requests') ? 'admin/requests' : 'admin');
}

$status = $_GET['status'] ?? 'open';
$where = $status === 'all' ? '1=1' : "r.status IN ('new','setup')";
$requests = qall("SELECT r.*, b.name AS business_name, t.name AS tool_name, p.name AS plan_name, p.price_cents, p.period, u.name AS user_name
                  FROM tool_requests r JOIN businesses b ON b.id = r.business_id JOIN tools t ON t.id = r.tool_id
                  LEFT JOIN plans p ON p.id = r.plan_id LEFT JOIN users u ON u.id = r.user_id
                  WHERE $where ORDER BY r.created_at DESC LIMIT 200");
render('admin/requests', compact('requests', 'status'), ['title' => 'Αιτήματα', 'nav' => 'admin-requests', 'area' => 'admin']);
