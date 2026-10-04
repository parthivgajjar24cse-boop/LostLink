<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$stmt = $pdo->query("SELECT id, iu_number, type, item_name, event_date, event_time, location, phone, status FROM items WHERE status = 'OPEN' ORDER BY created_at DESC LIMIT 12");
$items = $stmt->fetchAll();
$page_title = 'Welcome';

require __DIR__ . '/includes/header.php';
?>
<main class="guest-main">
    <section class="intro">
        <h1>University Lost &amp; Found</h1>
        <p>Report, locate, and communicate about lost and found property within the university community.</p>
        <?php if (!is_logged_in()): ?>
            <a class="button" href="register.php">Create Account</a>
            <a class="button secondary" href="login.php">Log In</a>
        <?php else: ?>
            <a class="button" href="dashboard.php">Open Dashboard</a>
        <?php endif; ?>
    </section>

    <section class="content">
        <div class="toolbar">
            <h2>Recent Reports</h2>
            <div class="filters">
                <input data-filter-table="#items" placeholder="Search items">
            </div>
        </div>

        <?php if (!$items): ?>
            <p class="empty">No item reports have been published yet.</p>
        <?php else: ?>
            <div class="table-wrap">
                <table id="items">
                    <thead>
                        <tr>
                            <th>IU No.</th>
                            <th>Item</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Location</th>
                            <th>Phone No.</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td>
                                    <a href="item.php?id=<?= $item['id'] ?>">
                                        <?= e($item['iu_number']) ?>
                                    </a>
                                </td>
                                <td class="<?= item_class($item['type']) ?>">
                                    <a class="item-link" href="item.php?id=<?= $item['id'] ?>">
                                        <?= e($item['item_name']) ?>
                                    </a>
                                </td>
                                <td><?= format_date($item['event_date']) ?></td>
                                <td><?= e(substr((string) $item['event_time'], 0, 5)) ?></td>
                                <td><?= e($item['location']) ?></td>
                                <td><?= e($item['phone']) ?></td>
                                <td class="<?= item_class($item['type']) ?>"><?= e($item['type']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>