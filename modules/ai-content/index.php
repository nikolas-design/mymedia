<?php
declare(strict_types=1);

// AI Content μέσα στην πύλη
require __DIR__ . '/lib.php';

$profile = ai_profile($bid);
$GLOBALS['module_ctx']['profile'] = $profile; // διαθέσιμο σε όλες τις προβολές
$routes = [
    ['',             'posts'],
    ['generate',     'generate'],
    ['posts/(\d+|new)', 'post'],
    ['profile',      'profile'],
];
foreach ($routes as [$pattern, $page]) {
    if (preg_match('#^' . $pattern . '$#', $subpath, $m)) {
        $params = array_slice($m, 1);
        require __DIR__ . '/pages/' . $page . '.php';
        exit;
    }
}
not_found();

function ai_tabs(string $current): string
{
    return module_tabs([['posts', '', 'Πλάνο posts'], ['generate', 'generate', '✨ Νέες προτάσεις'], ['profile', 'profile', 'Το ύφος σου']], $current);
}
