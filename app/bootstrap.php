<?php
declare(strict_types=1);

define('APP_ROOT', dirname(__DIR__));
date_default_timezone_set('Europe/Athens');
mb_internal_encoding('UTF-8');

if (!is_file(APP_ROOT . '/config.php')) {
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p style="font-family:sans-serif;padding:24px">Λείπει το <b>config.php</b>. '
       . 'Αντέγραψε το <code>config.sample.php</code> ως <code>config.php</code> και συμπλήρωσε τα στοιχεία της βάσης.</p>';
    exit;
}

$GLOBALS['config'] = require APP_ROOT . '/config.php';

$debug = !empty($GLOBALS['config']['debug']);
error_reporting(E_ALL);
ini_set('display_errors', $debug ? '1' : '0');
ini_set('log_errors', '1');

require __DIR__ . '/helpers.php';
require __DIR__ . '/icons.php';
require __DIR__ . '/db.php';
require __DIR__ . '/auth.php';
require __DIR__ . '/domain.php';
require __DIR__ . '/modules.php';

$https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

session_name('mymedia_sess');
session_set_cookie_params([
    'lifetime' => 0,
    'path'     => base_path() . '/',
    'secure'   => $https,
    'httponly' => true,
    'samesite' => 'Lax',
]);
ini_set('session.use_strict_mode', '1');
session_start();

header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: SAMEORIGIN');
header('Referrer-Policy: same-origin');

set_exception_handler(function (Throwable $e) use ($debug) {
    error_log((string) $e);
    http_response_code(500);
    header('Content-Type: text/html; charset=utf-8');
    echo '<p style="font-family:sans-serif;padding:24px">Κάτι πήγε στραβά. Δοκίμασε ξανά σε λίγο.</p>';
    if ($debug) {
        echo '<pre style="padding:0 24px;white-space:pre-wrap">' . e((string) $e) . '</pre>';
    }
});
