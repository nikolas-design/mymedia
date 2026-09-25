<?php
declare(strict_types=1);

// Δημόσιο πρόγραμμα για την ομάδα: /p/shifts/<token>?w=YYYY-MM-DD (μόνο δημοσιευμένες εβδομάδες)
require_once __DIR__ . '/lib.php';

$s = q1('SELECT * FROM sh_settings WHERE share_token = ?', [$subpath]);
$tool = q1("SELECT id FROM tools WHERE slug = 'shifts'");
if (!$s || !$tool || !active_subscription((int) $s['business_id'], (int) $tool['id'])) {
    not_found();
}
$bid = (int) $s['business_id'];
$monday = sh_monday(preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['w'] ?? '')) ? $_GET['w'] : date('Y-m-d'));
$unpublished = !$s['published_until'] || $s['published_until'] < date('Y-m-d', strtotime("$monday +6 days"));
$people = qall('SELECT * FROM sh_people WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
$grid = $unpublished ? [] : sh_week($bid, $monday);
$abs = $unpublished ? [] : sh_absences($bid, $monday);
$title = (string) qval('SELECT name FROM businesses WHERE id = ?', [$bid]);
$nav = url('p/shifts/' . $subpath);
header('X-Robots-Tag: noindex');
require __DIR__ . '/views/schedule_page.php';
