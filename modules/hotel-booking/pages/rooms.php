<?php
module_require_edit();
$rooms = qall('SELECT * FROM hb_rooms WHERE business_id = ? ORDER BY active DESC, sort, id', [$bid]);
module_page('rooms', compact('rooms'), 'Δωμάτια', 'rooms');
