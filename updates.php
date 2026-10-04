<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$active = 'updates';
$page_title = 'Updates';

$stmt = $pdo->prepare('SELECT * FROM notifications WHERE user_id = ? ORDER BY created_at DESC LIMIT 50');
$stmt->execute([current_user()['id']]);
$notes = $stmt->fetchAll();

$pdo->prepare('UPDATE notifications SET is_read = 1 WHERE user_id = ?')
    ->execute([current_user()['id']]);

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <main class="content">
        <h1>Updates</h1>

        <?php if (!$notes): ?>
            <p class="empty">You have no updates yet.</p>
        <?php else: ?>
            <?php foreach ($notes as $note): ?>
                <article class="notice">
                    <strong><?= e($note['title']) ?></strong>
                    <p><?= e($note['message']) ?></p>
                    <time><?= e(date('d/m/Y H:i', strtotime($note['created_at']))) ?></time>
                    <?php if ($note['related_item_id']): ?>
                        · <a href="item.php?id=<?= $note['related_item_id'] ?>">View item</a>
                    <?php endif; ?>
                </article>
            <?php endforeach; ?>
        <?php endif; ?>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
