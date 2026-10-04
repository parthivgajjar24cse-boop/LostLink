<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$active = 'lost';
$page_title = 'Lost Items';

$stmt = $pdo->prepare("SELECT id, iu_number, type, item_name, event_date, event_time, location, phone, status FROM items WHERE type = 'LOST' ORDER BY created_at DESC");
$stmt->execute();
$items = $stmt->fetchAll();

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php if (is_logged_in()): ?>
        <?php require __DIR__ . '/includes/sidebar.php'; ?>
    <?php endif; ?>

    <main class="content">
        <div class="toolbar">
            <h1>Lost Items</h1>
            <?php if (is_logged_in()): ?>
                <a class="button" href="report_lost.php">Report Lost Item</a>
            <?php endif; ?>
        </div>

        <div class="filters">
            <input data-filter-table="#item-table" placeholder="Search by item, IU number, category, or location">
        </div>
        <br>
        <?php require __DIR__ . '/includes/item_table.php'; ?>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
