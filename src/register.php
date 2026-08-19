<?php
require_once 'config.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'All fields are required.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        $stmt = $pdo->prepare('SELECT id FROM user WHERE username = :username OR email = :email');
        $stmt->execute(['username' => $username, 'email' => $email]);
        if ($stmt->fetch()) {
            $error = 'Username or email already exists.';
        } else {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);

            $stmt = $pdo->prepare('INSERT INTO user (username, email, password) VALUES (:username, :email, :password)');
            if ($stmt->execute(['username' => $username, 'email' => $email, 'password' => $hashed_password])) {
                $success = 'Registration successful. You can now <a href="login.php">login</a>.';
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JOIN THE SQUAD // GRIDIRON RAW</title>
    <link rel="stylesheet" href="./frontend/style.css">
</head>
<body>
    <!-- Header / Nav (Typical Navbar) -->
    <header class="navbar">
        <div class="navbar-brand">
            <a href="index.php" class="navbar-logo">GRIDIRON <span>RAW</span></a>
        </div>
        <nav class="navbar-menu">
            <a href="index.php" class="navbar-item">HOME</a>
            <a href="feed.php" class="navbar-item">THE TRENCHES</a>
            
            <?php if (isset($_SESSION['user_id'])): ?>
                <a href="create.php" class="navbar-item">+ POST</a>
                <span class="navbar-status">ID: <?= htmlspecialchars($_SESSION['username'] ?? 'USER') ?></span>
                <a href="logout.php" class="navbar-item navbar-btn">LOGOUT</a>
            <?php else: ?>
                <a href="login.php" class="navbar-item navbar-btn">LOGIN</a>
                <a href="register.php" class="navbar-item navbar-btn navbar-btn-accent">SIGN UP</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="auth-wrapper">
        <div class="brutalist-panel">
            <h2 class="panel-title">JOIN THE SQUAD</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" class="form-control" placeholder="CHOOSE CALLSIGN" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" class="form-control" placeholder="ENTER EMAIL ADDRESS" required>
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" class="form-control" placeholder="MINIMUM 8 CHARACTERS" required>
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" class="form-control" placeholder="RE-ENTER PASSPHRASE" required>
                </div>

                <div class="form-actions">
                    <a href="index.php" class="btn">CANCEL</a>
                    <button type="submit" class="btn btn-accent">Sign Up</button>
                </div>
            </form>
            
            <p class="auth-redirect">ALREADY ACTIVE? <a href="login.php">Login</a></p>
        </div>
    </main>
</body>
</html>