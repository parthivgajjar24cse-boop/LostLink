<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    redirect('index.php');
}

$stmt = $pdo->prepare('SELECT items.*, users.username FROM items JOIN users ON users.id = items.user_id WHERE items.id = ?');
$stmt->execute([$id]);
$item = $stmt->fetch();

if (!$item) {
    http_response_code(404);
    exit('Item not found.');
}

$active = strtolower($item['type']);
$page_title = $item['iu_number'];

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php if (is_logged_in()): ?>
        <?php require __DIR__ . '/includes/sidebar.php'; ?>
    <?php endif; ?>

    <main class="content">
        <div class="toolbar">
            <h1 class="<?= item_class($item['type']) ?>">
                <?= e($item['type']) ?>: <?= e($item['item_name']) ?>
            </h1>

            <?php if (is_logged_in() && current_user()['id'] != $item['user_id']): ?>
                <a class="button" href="chat.php?item_id=<?= $item['id'] ?>">Start Conversation</a>
            <?php elseif (!is_logged_in()): ?>
                <a class="button" href="login.php">Log in to Contact Reporter</a>
            <?php endif; ?>
        </div>

        <div class="details">
            <div>
                <dl>
                    <dt>IU Number</dt>
                    <dd><?= e($item['iu_number']) ?></dd>

                    <dt>Category</dt>
                    <dd><?= e($item['category']) ?></dd>

                    <dt>Date</dt>
                    <dd><?= format_date($item['event_date']) ?></dd>

                    <dt>Time</dt>
                    <dd><?= e(substr((string) $item['event_time'], 0, 5)) ?></dd>

                    <dt>Location</dt>
                    <dd><?= e($item['location']) ?></dd>

                    <dt>Description</dt>
                    <dd><?= nl2br(e($item['description'])) ?></dd>

                    <dt>Report Status</dt>
                    <dd><?= e($item['status']) ?></dd>

                    <dt>Contact Phone</dt>
                    <dd><?= e($item['phone']) ?></dd>
                </dl>
            </div>

            <div>
                <?php if ($item['image_path']): ?>
                    <img class="item-image" src="<?= e($item['image_path']) ?>" alt="Photograph of <?= e($item['item_name']) ?>">
                <?php else: ?>
                    <p class="empty">No photograph was provided.</p>
                <?php endif; ?>
            </div>
        </div>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
