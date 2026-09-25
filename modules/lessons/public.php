<?php
declare(strict_types=1);

// Σελίδα γονέα: /p/lessons/<token> — πρόγραμμα, παρουσίες, υπόλοιπο
$s = q1('SELECT * FROM ls_students WHERE token = ? AND active = 1', [$subpath]);
$tool = q1("SELECT id FROM tools WHERE slug = 'lessons'");
if (!$s || !$tool || !active_subscription((int) $s['business_id'], (int) $tool['id'])) {
    not_found();
}
$bizName = (string) qval('SELECT name FROM businesses WHERE id = ?', [$s['business_id']]);
$groups = qall('SELECT g.* FROM ls_enrollments e JOIN ls_groups g ON g.id = e.group_id WHERE e.student_id = ? AND g.active = 1', [$s['id']]);
$recent = qall('SELECT a.day, a.present, g.name FROM ls_attendance a JOIN ls_groups g ON g.id = a.group_id WHERE a.student_id = ? ORDER BY a.day DESC LIMIT 12', [$s['id']]);
$balance = (int) qval('SELECT COALESCE(SUM(amount_cents - paid_cents),0) FROM ls_charges WHERE student_id = ?', [$s['id']]);
header('X-Robots-Tag: noindex');
public_page(__DIR__ . '/views/public_parent.php', compact('s', 'bizName', 'groups', 'recent', 'balance'), $bizName, '#4c5fd8');
