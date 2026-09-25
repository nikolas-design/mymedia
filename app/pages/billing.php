<?php
$biz = require_business();
require_role('owner');
$bid = (int) $biz['id'];
$subs = business_subscriptions($bid);
$monthly = array_sum(array_map('monthly_cents', $subs));
$invoices = qall('SELECT * FROM invoices WHERE business_id = ? ORDER BY issued_on DESC, id DESC', [$bid]);
$balance = array_sum(array_map(fn($i) => $i['status'] === 'issued' ? (int) $i['total_cents'] : 0, $invoices));
render('billing', compact('subs', 'monthly', 'invoices', 'balance'), ['title' => 'Χρεώσεις', 'nav' => 'billing']);
