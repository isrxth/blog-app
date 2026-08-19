<?php
require_once 'config.php';

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('
    SELECT blogPost.*, user.username 
    FROM blogPost 
    JOIN user ON blogPost.user_id = user.id 
    WHERE blogPost.id = ?
');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die('Post not found.');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($post['title']) ?> // GRIDIRON RAW</title>
    <link rel="stylesheet" href="frontend/style.css">
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
                <a href="register.php" class="navbar-item navbar-btn">SIGN UP</a>
            <?php endif; ?>
        </nav>
    </header>

    <main class="article-container">
        <a href="index.php" class="btn btn-sm back-link">&larr; BACK TO TRENCHES</a>
        
        <article class="article-view">
            <div class="article-category">[<?= htmlspecialchars(strtoupper($post['category'] ?? 'Analysis')) ?>]</div>
            <h1 class="article-title"><?= htmlspecialchars($post['title']) ?></h1>
            
            <div class="article-meta">
                BY <strong><?= htmlspecialchars($post['username']) ?></strong> // TRANSMITTED <?= date('d M Y', strtotime($post['created_at'])) ?>
            </div>
            
            <div class="article-content"><?= htmlspecialchars($post['content']) ?></div>
        </article>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer-status">SYSTEM ACCESS // USER GRANTED</div>
        <p>&copy; <?= date('Y') ?> GRIDIRON RAW // RAW ANALYSIS & UNFILTERED COMMENTARY.</p>
    </footer>
</body>
</html>