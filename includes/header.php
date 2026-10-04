<?php
require_once __DIR__ . '/auth.php';

$page_title = $page_title ?? 'LostLink';
$flash = get_flash();
?>
<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($page_title) ?> | LostLink</title>
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/mobile.css">
</head>
<body>
<header class="site-header">
    <a class="brand" href="index.php">LOSTLINK</a>
    <span>University Lost &amp; Found System</span>
    <nav>
        <?php if (is_logged_in()): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="logout.php">Logout</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="register.php">Register</a>
        <?php endif; ?>
        <button class="theme-toggle" type="button" data-theme-toggle aria-label="Switch color theme">Dark mode</button>
    </nav>
</header>

<?php if ($flash): ?>
    <div class="flash <?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
<?php endif; ?>
