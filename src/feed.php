<?php
require_once 'config.php';

$stmt = $pdo->query('
    SELECT blogPost.*, user.username 
    FROM blogPost 
    JOIN user ON blogPost.user_id = user.id
    ORDER BY blogPost.created_at DESC
');
$posts = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>THE TRENCHES // GRIDIRON RAW</title>
    <link rel="stylesheet" href="./frontend/style.css">
</head>
<body>
    <!-- Breaking News Ticker -->
    <div class="ticker-wrap">
        <div class="ticker">
            <span class="ticker-item">SYSTEM STATUS: ACTIVE</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">GRIDIRON RAW PORTAL ONLINE</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">TACTICAL FOOTBALL BREAKDOWNS INCOMING</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">INITIALIZING TACTICAL PROTOCOL</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">OPINION: THE RUN-PASS OPTION EXPOSED</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">ANALYSIS: DEFENSIVE COVERAGE DECONSTRUCTED</span>
            <span class="ticker-item">//</span>
            <span class="ticker-item">DATA ROOM: SPECIAL TEAMS METRICS LOADED</span>
        </div>
    </div>

    <!-- Header / Nav (Typical Navbar) -->
    <header class="navbar">
        <div class="navbar-brand">
            <a href="index.php" class="navbar-logo">GRIDIRON <span>RAW</span></a>
        </div>
        <nav class="navbar-menu">
            <a href="index.php" class="navbar-item">HOME</a>
            <a href="feed.php" class="navbar-item active">THE TRENCHES</a>
            
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

    <!-- Main Feed -->
    <main>
        <h2 class="section-title">LATEST REPORTS</h2>
        
        <?php if (empty($posts)): ?>
            <div class="brutalist-panel" style="text-align: center;">
                <p>NO REPORTS FOUND IN THE TRENCHES. PROTOCOL EMPTY.</p>
            </div>
        <?php else: ?>
            <div class="trenches-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="post-card">
                        <div class="post-header">
                            <span class="category-badge">[<?= htmlspecialchars(strtoupper($post['category'] ?? 'Analysis')) ?>]</span>
                            <h3 class="post-title">
                                <a href="view.php?id=<?= $post['id'] ?>">
                                    <?= htmlspecialchars($post['title']) ?>
                                </a>
                            </h3>
                            <div class="post-meta">
                                BY <strong><?= htmlspecialchars($post['username']) ?></strong> // <?= date('d M Y', strtotime($post['created_at'])) ?>
                            </div>
                        </div>
                        
                        <p class="post-snippet">
                            <?= htmlspecialchars(substr($post['content'], 0, 150)) ?><?= strlen($post['content']) > 150 ? '...' : '' ?>
                        </p>
                        
                        <div class="post-actions">
                            <a href="view.php?id=<?= $post['id'] ?>" class="btn btn-sm">READ REPORT</a>
                            
                            <!-- Show Edit/Delete ONLY if the current logged-in user owns this post -->
                            <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
                                <a href="edit.php?id=<?= $post['id'] ?>" class="btn btn-sm">EDIT</a>
                                <a href="delete.php?id=<?= $post['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('DESTRUCT POST PROTOCOL: ARE YOU SURE?')">DESTRUCT</a>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer-status">SYSTEM ACCESS // USER GRANTED</div>
        <p>&copy; <?= date('Y') ?> GRIDIRON RAW // RAW ANALYSIS & UNFILTERED COMMENTARY.</p>
    </footer>
</body>
</html>