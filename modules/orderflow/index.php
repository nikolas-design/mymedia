<?php
declare(strict_types=1);

// OrderFlow μέσα στην πύλη
const OF_STATUS = [
    'draft'     => ['Πρόχειρη', 'grey'],
    'sent'      => ['Στάλθηκε', 'purple'],
    'received'  => ['Παραλήφθηκε', 'green'],
    'cancelled' => ['Ακυρώθηκε', 'grey'],
];

$routes = [
    ['',                    'overview'],
    ['new/(\d+)',           'order_new'],
    ['orders',              'orders'],
    ['orders/(\d+)',        'order'],
    ['suppliers',           'suppliers'],
    ['suppliers/(\d+|new)', 'supplier'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();


function of_tabs(string $current): string
{
    return module_tabs([
        ['overview', '', 'Νέα παραγγελία'],
        ['orders', 'orders', 'Ιστορικό'],
        ['suppliers', 'suppliers', 'Προμηθευτές & είδη'],
    ], $current);
}

function of_qty(string|float $q): string
{
    $q = (float) $q;
    return rtrim(rtrim(number_format($q, 2, ',', ''), '0'), ',');
}

/** Το κείμενο της παραγγελίας, όπως το λαμβάνει ο προμηθευτής */
function of_order_text(array $order, array $lines, string $bizName): string
{
    $t = "Παραγγελία από: $bizName\n";
    if ($order['delivery_date']) {
        $t .= 'Παράδοση: ' . date_gr($order['delivery_date']) . "\n";
    }
    $t .= "\n";
    foreach ($lines as $l) {
        $t .= '• ' . of_qty($l['qty']) . ' ' . $l['unit'] . ' ' . $l['name'] . "\n";
    }
    if ($order['note']) {
        $t .= "\nΣημείωση: " . $order['note'] . "\n";
    }
    return $t . "\nΕυχαριστούμε!";
}

function of_order_total(array $lines): int
{
    $sum = 0;
    foreach ($lines as $l) {
        $sum += (int) round(((float) $l['qty']) * (int) ($l['price_cents'] ?? 0));
    }
    return $sum;
}
