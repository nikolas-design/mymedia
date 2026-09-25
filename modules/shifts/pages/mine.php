<?php
$me = sh_me($bid, $user);
$monday = sh_monday(preg_match('/^\d{4}-\d{2}-\d{2}$/', (string) ($_GET['w'] ?? '')) ? $_GET['w'] : date('Y-m-d'));
$shifts = $me ? qall('SELECT * FROM sh_shifts WHERE person_id = ? AND day >= ? ORDER BY day, start_time LIMIT 30', [$me['id'], $monday]) : [];
$settings = sh_settings($bid);
module_page('mine', compact('me', 'shifts', 'monday', 'settings'), 'Οι βάρδιες μου', 'mine');
