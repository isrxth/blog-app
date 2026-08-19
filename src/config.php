<?php
function getEnvVar($key, $default = null) {
    $val = getenv($key);
    if ($val !== false) {
        return $val;
    }
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    return $default;
}

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0) {
            continue;
        }
        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value);
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv("$name=$value");
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

$host = getEnvVar('DB_HOST', 'db');
$db   = getEnvVar('DB_NAME', 'blog_db');
$user = getEnvVar('DB_USER', 'root');
$pass = getEnvVar('DB_PASS', 'rootpassword');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $colCheck = $pdo->query("SHOW COLUMNS FROM blogPost LIKE 'category'");
    if (!$colCheck->fetch()) {
        $pdo->exec("ALTER TABLE blogPost ADD COLUMN category VARCHAR(50) NOT NULL DEFAULT 'Analysis'");
    }
} catch (PDOException $e) {
    die("Database Connection Failed: " . $e->getMessage());
}

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION['user_id']) && !isset($_SESSION['username'])) {
    try {
        $stmt = $pdo->prepare("SELECT username FROM user WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $u = $stmt->fetch();
        if ($u) {
            $_SESSION['username'] = $u['username'];
        }
    } catch (PDOException $e) {
    }
}
?>