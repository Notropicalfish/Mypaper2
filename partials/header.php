<?php
// MyPaper - Header Partial v2.0 by Peelish Studios
if (session_status() === PHP_SESSION_NONE) { session_start(); }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - MyPaper' : 'MyPaper by Peelish Studios'; ?></title>
    <!-- Font Awesome for Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Main Stylesheet (with cache busting) -->
    <link rel="stylesheet" href="style.css?v=<?php echo time(); ?>">
</head>
<body>
    <header class="site-header">
        <div class="container header-container">
            <div class="logo">
                <a href="index.php"><i class="fas fa-image"></i> MyPaper</a>
            </div>
            <nav class="main-nav">
                <ul>
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <li><a href="index.php?page=upload" class="<?php echo ($page === 'upload') ? 'active' : ''; ?>">Upload</a></li>
                        <li><a href="index.php?page=profile" class="<?php echo ($page === 'profile') ? 'active' : ''; ?>">Profile</a></li>
                        <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] === true): ?>
                            <li><a href="index.php?page=admin" class="<?php echo ($page === 'admin') ? 'active' : ''; ?>">Admin Panel</a></li>
                        <?php endif; ?>
                        <li><a href="index.php?action=logout">Logout</a></li>
                    <?php else: ?>
                        <li><a href="index.php?page=login" class="<?php echo ($page === 'login') ? 'active' : ''; ?>">Login</a></li>
                        <li><a href="index.php?page=register" class="<?php echo ($page === 'register') ? 'active' : ''; ?>" class="button button-primary">Sign Up</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
        </div>
    </header>
    <main class="container">

