<?php
require_once 'config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>GRIDIRON RAW // UNFILTERED FOOTBALL ANALYTICS</title>
    <link rel="stylesheet" href="frontend/style.css">
</head>
<body>
    <!-- Header / Nav (Typical Navbar) -->
    <header class="navbar">
        <div class="navbar-brand">
            <a href="index.php" class="navbar-logo">GRIDIRON <span>RAW</span></a>
        </div>
        <nav class="navbar-menu">
            <a href="index.php" class="navbar-item active">HOME</a>
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

    <!-- Main Landing Experience -->
    <main>
        <!-- Hero Split Section -->
        <section class="hero-section">
            <div class="hero-left">
                <h1 class="hero-title">GRIDIRON <span>RAW</span></h1>
                <p class="hero-subtitle">
                    A raw, high-octane football portal designed for brutal tactical analysis, unfiltered commentary, and raw match metrics.
                </p>
                <div class="hero-cta">
                    <a href="feed.php" class="btn">ENTER THE TRENCHES</a>
                </div>
            </div>
            <div class="hero-right">
                <div class="hero-image-frame">
                    <img src="frontend/assets/hero.jpg" alt="GRIDIRON RAW Tactical Board">
                </div>
            </div>
        </section>

        <!-- Features Section -->
        <section class="features-section">
            <h2 class="features-title">OPERATIONAL PILLARS</h2>
            <div class="features-grid">
                <div class="feature-card">
                    <span class="feature-number">[01]</span>
                    <h3>RAW ANALYSIS</h3>
                    <p>No corporate sugar-coating. Straight, tactical breakdowns, passing maps, and defensive formations analyzed directly from tape.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-number">[02]</span>
                    <h3>OPINION SECTION</h3>
                    <p>High-contrast commentary. Live field dispatches from soccer purists who dissect every tactical decision on the pitch.</p>
                </div>
                <div class="feature-card">
                    <span class="feature-number">[03]</span>
                    <h3>THE DATA ROOM</h3>
                    <p>Monospaced match metrics. Parsing xG maps, transition speed indices, and high press intensity metrics.</p>
                </div>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="site-footer">
        <div class="site-footer-status">SYSTEM STATUS // OPTIMAL // PORTAL INITIALIZED</div>
        <p>&copy; <?= date('Y') ?> GRIDIRON RAW // RAW ANALYSIS & UNFILTERED COMMENTARY.</p>
    </footer>
</body>
</html>