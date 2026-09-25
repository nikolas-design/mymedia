<?php
declare(strict_types=1);

function db(): PDO
{
    static $pdo = null;
    if ($pdo === null) {
        $c = $GLOBALS['config']['db'];
        $connect = fn(string $host) => new PDO(
            'mysql:host=' . $host . ';dbname=' . $c['name'] . ';charset=utf8mb4',
            $c['user'],
            $c['pass'],
            [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ]
        );
        try {
            $pdo = $connect($c['host']);
        } catch (PDOException $e) {
            // Με "localhost" η PHP ψάχνει socket αρχείο, που σε κάποια hosting είναι αλλού (σφάλμα 2002).
            // Τότε δοκιμάζουμε σύνδεση μέσω TCP.
            if ($c['host'] !== 'localhost' || !str_contains($e->getMessage(), '2002')) {
                throw $e;
            }
            $pdo = $connect('127.0.0.1');
        }
        $pdo->exec("SET time_zone = '" . date('P') . "'");
    }
    return $pdo;
}

function q(string $sql, array $params = []): PDOStatement
{
    $st = db()->prepare($sql);
    $st->execute($params);
    return $st;
}

/** Μία γραμμή ή null */
function q1(string $sql, array $params = []): ?array
{
    $row = q($sql, $params)->fetch();
    return $row === false ? null : $row;
}

function qall(string $sql, array $params = []): array
{
    return q($sql, $params)->fetchAll();
}

/** Η πρώτη στήλη της πρώτης γραμμής */
function qval(string $sql, array $params = []): mixed
{
    $v = q($sql, $params)->fetchColumn();
    return $v === false ? null : $v;
}

function setting(string $key, string $default = ''): string
{
    static $cache = null;
    if ($cache === null) {
        $cache = [];
        foreach (qall('SELECT k, v FROM settings') as $r) {
            $cache[$r['k']] = $r['v'];
        }
    }
    return $cache[$key] ?? $default;
}
