<?php
require_once 'config.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$id = $_GET['id'] ?? null;
if (!$id) {
    header('Location: index.php');
    exit;
}

$stmt = $pdo->prepare('SELECT * FROM blogPost WHERE id = ?');
$stmt->execute([$id]);
$post = $stmt->fetch();

if (!$post) {
    die('Post not found.');
}

if ($post['user_id'] != $_SESSION['user_id']) {
    die('Unauthorized: You can only edit your own posts.');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title    = trim($_POST['title']);
    $content  = trim($_POST['content']);
    $category = trim($_POST['category'] ?? 'Analysis');

    $allowed_categories = ['Analysis', 'Opinion', 'Data Room'];
    if (!in_array($category, $allowed_categories)) {
        $category = 'Analysis';
    }

    if (empty($title) || empty($content)) {
        $error = 'Please fill in all fields.';
    } else {
        $stmt = $pdo->prepare('UPDATE blogPost SET title = ?, content = ?, category = ? WHERE id = ? AND user_id = ?');
        if ($stmt->execute([$title, $content, $category, $id, $_SESSION['user_id']])) {
            header('Location: index.php');
            exit;
        } else {
            $error = 'Failed to update post.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EDIT PROTOCOL // GRIDIRON RAW</title>
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

    <main class="editor-wrapper">
        <div class="brutalist-panel">
            <h2 class="panel-title">EDIT INTEL REPORT</h2>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" action="edit.php?id=<?= $id ?>">
                <div class="form-group">
                    <label for="category">Tactical Section</label>
                    <select name="category" id="category" class="form-control" required>
                        <option value="Analysis" <?= ($post['category'] ?? 'Analysis') === 'Analysis' ? 'selected' : '' ?>>Analysis (Deep-dive Breakdowns)</option>
                        <option value="Opinion" <?= ($post['category'] ?? '') === 'Opinion' ? 'selected' : '' ?>>Opinion (Raw Commentary)</option>
                        <option value="Data Room" <?= ($post['category'] ?? '') === 'Data Room' ? 'selected' : '' ?>>Data Room (Stats & Metrics)</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="title">Report Title</label>
                    <input type="text" name="title" id="title" class="form-control" value="<?= htmlspecialchars($post['title']) ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="content">Intel Content</label>
                    <textarea name="content" id="content" class="form-control" required><?= htmlspecialchars($post['content']) ?></textarea>
                </div>
                
                <div class="form-actions">
                    <a href="index.php" class="btn">DISCARD</a>
                    <button type="submit" class="btn btn-accent">UPDATE REPORT</button>
                </div>
            </form>
        </div>
    </main>
</body>
</html>