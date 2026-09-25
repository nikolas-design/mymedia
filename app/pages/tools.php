<?php
$biz = require_business();
$tools = qall("SELECT * FROM tools WHERE status <> 'hidden' ORDER BY sort");
$states = tool_states((int) $biz['id']);
render('tools', compact('tools', 'states'), ['title' => 'Εργαλεία', 'nav' => 'tools']);
