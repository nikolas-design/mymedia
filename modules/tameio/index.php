<?php
declare(strict_types=1);

// Ταμείο μέσα στην πύλη. Μέλη: κλείσιμο και έξοδα. Ιδιοκτήτης/υπεύθυνος: και αναφορές.
const TM_CATEGORIES = ['Προμήθειες', 'Αναλώσιμα', 'Λογαριασμοί', 'Μισθοδοσία', 'Ενοίκιο', 'Συντήρηση', 'Μεταφορικά', 'Διάφορα'];

const TM_PAYMENT = ['cash' => 'Μετρητά', 'card' => 'Κάρτα', 'bank' => 'Τράπεζα'];

$routes = [
    ['',                'overview'],
    ['close',           'close'],
    ['closings/(\d+)',  'closing'],
    ['expenses',        'expenses'],
    ['expenses/(\d+)',  'expense'],
    ['report',          'report'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();


function tm_tabs(string $current): string
{
    $tabs = [['overview', '', 'Σήμερα'], ['close', 'close', 'Κλείσιμο ταμείου'], ['expenses', 'expenses', 'Έξοδα']];
    if ($GLOBALS['module_ctx']['canEdit']) {
        $tabs[] = ['report', 'report', 'Αναφορές'];
    }
    return module_tabs($tabs, $current);
}

/** Αναμενόμενα μετρητά στο συρτάρι */
function tm_expected(array $c): int
{
    return (int) $c['opening_cents'] + (int) $c['cash_cents'] - (int) $c['cash_expenses_cents'];
}

function tm_sales(array $c): int
{
    return (int) $c['cash_cents'] + (int) $c['card_cents'] + (int) $c['other_cents'];
}

function tm_diff_pill(?int $diff): string
{
    if ($diff === null) {
        return pill('Χωρίς καταμέτρηση', 'grey');
    }
    if (abs($diff) < 50) {
        return pill('Σωστό', 'green');
    }
    return pill(($diff > 0 ? 'Περίσσευμα ' : 'Έλλειμμα ') . money(abs($diff)), $diff > 0 ? 'orange' : 'red');
}

/** Ποσό που μπορεί να είναι αρνητικό */
function tm_money(int $cents): string
{
    return ($cents < 0 ? '−' : '') . money(abs($cents));
}
