<?php
$biz = require_business();
$items = qall('SELECT * FROM notifications WHERE business_id = ? ORDER BY id DESC LIMIT 50', [$biz['id']]);
q('UPDATE notifications SET read_at = NOW() WHERE business_id = ? AND read_at IS NULL', [$biz['id']]);
render('notifications', compact('items'), ['title' => 'Ειδοποιήσεις', 'nav' => 'notifications']);
