<?php
declare(strict_types=1);

// Lessons Manager μέσα στην πύλη
const LS_DAYS = [1 => 'Δευ', 2 => 'Τρί', 3 => 'Τετ', 4 => 'Πέμ', 5 => 'Παρ', 6 => 'Σάβ', 7 => 'Κυρ'];

$routes = [
    ['',                     'today'],
    ['groups',               'groups'],
    ['groups/(\d+|new)',     'group'],
    ['attendance/(\d+)',     'attendance'],
    ['students',             'students'],
    ['students/(\d+|new)',   'student'],
    ['fees',                 'fees'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function ls_tabs(string $current): string
{
    $tabs = [['today', '', 'Σήμερα'], ['groups', 'groups', 'Τμήματα'], ['students', 'students', 'Μαθητές']];
    if ($GLOBALS['module_ctx']['canEdit']) {
        $tabs[] = ['fees', 'fees', 'Δίδακτρα'];
    }
    return module_tabs($tabs, $current);
}

function ls_days_label(?string $weekdays): string
{
    return implode(', ', array_map(fn($d) => LS_DAYS[(int) $d] ?? '', array_filter(explode(',', (string) $weekdays))));
}

/** Υπόλοιπο μαθητή (χρεώσεις − πληρωμές) */
function ls_balance(int $studentId): int
{
    return (int) qval('SELECT COALESCE(SUM(amount_cents - paid_cents),0) FROM ls_charges WHERE student_id = ?', [$studentId]);
}
