<?php
function getEnvVar($key, $default = null)
{
    $val = getenv($key);
    if ($val !== false) {
        return $val;
    }
    if (isset($_SERVER[$key])) {
        return $_SERVER[$key];
    }
    if (isset($_ENV[$key])) {
        return $_ENV[$key];
    }
    return $default;
}

if (file_exists(__DIR__ . '/.env')) {
    $lines = file(__DIR__ . '/.env', FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        if (strpos(trim($line), '#') === 0 || !str_contains($line, '=')) {
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
$port = getEnvVar('DB_PORT', '3306');
$db = getEnvVar('DB_NAME', 'blog_db');
$user = getEnvVar('DB_USER', 'root');
// Checks DB_PASS first, then falls back to DB_PASSWORD
$pass = getEnvVar('DB_PASS', getEnvVar('DB_PASSWORD', 'rootpassword'));

try {
    $dsn = "mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_TIMEOUT => 10,
        // Required for Aiven SSL
        PDO::MYSQL_ATTR_SSL_CA => true,
        PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
    ];

    $pdo = new PDO($dsn, $user, $pass, $options);

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