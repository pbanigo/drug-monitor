<?php
require_once __DIR__ . '/db.php';

// ---------------------------------------------------------------------------
// Session + current-user bootstrap
// ---------------------------------------------------------------------------

// Ensure there is always a current user: a returning one, a guest from the
// cookie, or a freshly created guest. Called once from functions.php after the
// drug helpers (including seed_user_drugs) are defined.
// Are we being served over HTTPS (directly or behind a proxy)?
function is_https()
{
    return (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['SERVER_PORT'] ?? '') == 443)
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
}

function auth_bootstrap()
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure'   => is_https(),
        ]);
        session_start();
    }

    if (!empty($_SESSION['user_id']) && auth_find_user($_SESSION['user_id'])) {
        auth_touch($_SESSION['user_id']);
        return;
    }

    // Returning guest via cookie
    if (!empty($_COOKIE['dm_guest'])) {
        $user = auth_find_by_guest_token($_COOKIE['dm_guest']);
        if ($user) {
            $_SESSION['user_id'] = (int) $user['id'];
            auth_touch($user['id']);
            return;
        }
    }

    // Brand new guest
    $token = bin2hex(random_bytes(32));
    $stmt = get_db()->prepare('INSERT INTO users (guest_token, last_seen) VALUES (?, NOW())');
    $stmt->execute([$token]);
    $id = (int) get_db()->lastInsertId();

    setcookie('dm_guest', $token, [
        'expires'  => time() + 31536000, // 1 year
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    $_COOKIE['dm_guest'] = $token;
    $_SESSION['user_id'] = $id;

    seed_user_drugs($id); // starter sample drugs (defined in functions.php)
}

function current_user_id()
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : 0;
}

function current_user()
{
    static $cache = null;
    if ($cache === null || $cache['id'] != current_user_id()) {
        $cache = auth_find_user(current_user_id());
    }
    return $cache;
}

function is_logged_in()
{
    $u = current_user();
    return $u && !empty($u['username']);
}

function is_guest()
{
    return !is_logged_in();
}

// ---------------------------------------------------------------------------
// Login / register / logout
// ---------------------------------------------------------------------------

function auth_login($username, $password)
{
    $user = auth_find_by_username($username);
    if ($user && !empty($user['password_hash']) && password_verify($password, $user['password_hash'])) {
        session_regenerate_id(true);
        $_SESSION['user_id'] = (int) $user['id'];
        return true;
    }
    return false;
}

// Upgrade the current guest account into a real one. Returns true or an error.
function auth_register($username, $password)
{
    $username = trim($username);

    if (!preg_match('/^[A-Za-z0-9_.-]{3,30}$/', $username)) {
        return 'Username must be 3-30 characters (letters, numbers, . _ -).';
    }
    if (strlen($password) < 8) {
        return 'Password must be at least 8 characters.';
    }
    if (is_logged_in()) {
        return 'You already have an account.';
    }
    if (auth_find_by_username($username)) {
        return 'That username is already taken.';
    }

    $stmt = get_db()->prepare(
        'UPDATE users SET username = ?, password_hash = ?, guest_token = NULL WHERE id = ?'
    );
    $stmt->execute([$username, password_hash($password, PASSWORD_DEFAULT), current_user_id()]);

    // No longer a guest: drop the guest cookie.
    setcookie('dm_guest', '', time() - 3600, '/');
    session_regenerate_id(true);
    return true;
}

function auth_logout()
{
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 3600, $p['path'], $p['domain'], $p['secure'], $p['httponly']);
    }
    session_destroy();
    setcookie('dm_guest', '', time() - 3600, '/'); // fresh guest next visit
}

// ---------------------------------------------------------------------------
// CSRF
// ---------------------------------------------------------------------------

function csrf_token()
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field()
{
    return '<input type="hidden" name="csrf" value="' . htmlspecialchars(csrf_token(), ENT_QUOTES) . '">';
}

function csrf_check()
{
    return !empty($_POST['csrf']) && hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf']);
}

// ---------------------------------------------------------------------------
// Small DB lookups
// ---------------------------------------------------------------------------

function auth_find_user($id)
{
    $stmt = get_db()->prepare('SELECT * FROM users WHERE id = ?');
    $stmt->execute([(int) $id]);
    return $stmt->fetch() ?: null;
}

function auth_find_by_username($username)
{
    $stmt = get_db()->prepare('SELECT * FROM users WHERE username = ?');
    $stmt->execute([$username]);
    return $stmt->fetch() ?: null;
}

function auth_find_by_guest_token($token)
{
    $stmt = get_db()->prepare('SELECT * FROM users WHERE guest_token = ?');
    $stmt->execute([$token]);
    return $stmt->fetch() ?: null;
}

function auth_touch($id)
{
    get_db()->prepare('UPDATE users SET last_seen = NOW() WHERE id = ?')->execute([(int) $id]);
}
