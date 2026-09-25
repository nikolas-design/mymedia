<?php
$monday = sh_monday(preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['w'] ?? '')) ? $_GET['w'] : date('Y-m-d'));
$people = qall('SELECT * FROM sh_people WHERE business_id = ? AND active = 1 ORDER BY name', [$bid]);
$grid = sh_week($bid, $monday);
$abs = sh_absences($bid, $monday);
$title = $business['name'];
require __DIR__ . '/../views/schedule_page.php';
exit;
