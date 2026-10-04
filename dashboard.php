<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$active = '';
$page_title = 'Dashboard';

$count = $pdo->query("SELECT COUNT(*) FROM items WHERE status = 'OPEN'")->fetchColumn();

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <main class="content">
        <h1>Welcome, <?= e(current_user()['username']) ?></h1>
        <p>Use the navigation to browse active reports, view updates, manage your account, or report an item.</p>
        <p><strong><?= e((string)$count) ?></strong> active item reports are currently available.</p>
        <p>
            <a class="button" href="report_lost.php">Report Lost Item</a>
            <a class="button secondary" href="report_found.php">Report Found Item</a>
        </p>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
