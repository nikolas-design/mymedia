<?php
declare(strict_types=1);

const ROLE_LABELS = ['owner' => 'Ιδιοκτήτης', 'manager' => 'Υπεύθυνος', 'member' => 'Μέλος'];

function current_user(): ?array
{
    static $user = false;
    if ($user === false) {
        $user = null;
        if (!empty($_SESSION['uid'])) {
            $user = q1('SELECT * FROM users WHERE id = ? AND active = 1', [$_SESSION['uid']]);
            if (!$user) {
                unset($_SESSION['uid']);
            }
        }
    }
    return $user;
}

function is_admin(): bool
{
    $u = current_user();
    return $u !== null && (int) $u['is_admin'] === 1;
}

function login_user(array $user): void
{
    session_regenerate_id(true);
    $_SESSION['uid'] = (int) $user['id'];
    unset($_SESSION['bid']);
    q('UPDATE users SET last_login_at = NOW() WHERE id = ?', [$user['id']]);
}

function logout_user(): void
{
    $_SESSION = [];
    session_regenerate_id(true);
}

function require_login(): array
{
    $u = current_user();
    if (!$u) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        redirect('login');
    }
    return $u;
}

function require_admin(): array
{
    $u = require_login();
    if (!is_admin()) {
        forbidden();
    }
    return $u;
}

/** Οι επιχειρήσεις όπου ο χρήστης είναι ενεργό μέλος */
function user_businesses(int $userId): array
{
    return qall(
        'SELECT b.*, m.role FROM memberships m JOIN businesses b ON b.id = m.business_id
         WHERE m.user_id = ? AND m.active = 1 ORDER BY b.name',
        [$userId]
    );
}

/**
 * Η επιχείρηση που βλέπει τώρα ο χρήστης, μαζί με τον ρόλο του (key "role").
 * Null αν δεν ανήκει σε καμία.
 */
function current_business(): ?array
{
    static $biz = false;
    if ($biz !== false) {
        return $biz;
    }
    $biz = null;
    $u = current_user();
    if (!$u) {
        return null;
    }
    $list = user_businesses((int) $u['id']);
    foreach ($list as $b) {
        if ((int) $b['id'] === (int) ($_SESSION['bid'] ?? 0)) {
            $biz = $b;
        }
    }
    if ($biz === null && $list) {
        $biz = $list[0];
        $_SESSION['bid'] = (int) $biz['id'];
    }
    return $biz;
}

/** Για σελίδες πελάτη: απαιτεί χρήστη που ανήκει σε επιχείρηση */
function require_business(): array
{
    require_login();
    $b = current_business();
    if (!$b) {
        if (is_admin()) {
            redirect('admin');
        }
        render('message', [
            'heading' => 'Δεν ανήκεις σε καμία επιχείρηση',
            'text'    => 'Ο λογαριασμός σου δεν είναι συνδεδεμένος με επιχείρηση. Ζήτα πρόσκληση από τον ιδιοκτήτη.',
        ], ['title' => 'Χωρίς επιχείρηση']);
    }
    return $b;
}

/** Ο ρόλος του χρήστη στην τρέχουσα επιχείρηση είναι ένας από αυτούς; */
function has_role(string ...$roles): bool
{
    $b = current_business();
    return $b !== null && in_array($b['role'], $roles, true);
}

function require_role(string ...$roles): void
{
    if (!has_role(...$roles)) {
        forbidden();
    }
}

/* ---------- Προστασία από επαναλαμβανόμενες αποτυχίες login ---------- */

function login_blocked(string $email): bool
{
    $ip = $_SERVER['REMOTE_ADDR'] ?? '';
    $n = (int) qval(
        'SELECT COUNT(*) FROM login_attempts
         WHERE (email = ? OR ip = ?) AND attempted_at > (NOW() - INTERVAL 15 MINUTE)',
        [$email, $ip]
    );
    return $n >= 8;
}

function login_failed(string $email): void
{
    q('INSERT INTO login_attempts (email, ip, attempted_at) VALUES (?, ?, NOW())', [$email, $_SERVER['REMOTE_ADDR'] ?? '']);
    q('DELETE FROM login_attempts WHERE attempted_at < (NOW() - INTERVAL 1 DAY)');
}

function password_problem(string $pass): ?string
{
    if (mb_strlen($pass) < 10) {
        return 'Ο κωδικός πρέπει να έχει τουλάχιστον 10 χαρακτήρες.';
    }
    return null;
}
