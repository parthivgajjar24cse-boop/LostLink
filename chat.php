<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$itemId = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);
if (!$itemId) {
    redirect('dashboard.php');
}

$stmt = $pdo->prepare(
    'SELECT i.*, 
            u.id AS reporter_id, 
            u.username, 
            u.department, 
            u.semester, 
            u.batch, 
            u.enrollment_no, 
            u.phone AS reporter_phone 
     FROM items i 
     JOIN users u ON u.id = i.user_id 
     WHERE i.id = ?'
);
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item || $item['reporter_id'] == current_user()['id']) {
    set_flash('error', 'A conversation must be with another user about an item.');
    redirect('dashboard.php');
}

$active = '';
$page_title = 'Conversation';

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <main class="content">
        <h1>Conversation: <?= e($item['iu_number']) ?></h1>

        <section class="chat-info">
            <strong><?= e($item['username']) ?></strong><br>
            Department: <?= e($item['department']) ?><br>
            Semester: <?= e($item['semester']) ?><br>
            Batch: <?= e($item['batch']) ?><br>
            Enrollment No.: <?= e($item['enrollment_no']) ?><br>
            Phone: <?= e($item['reporter_phone']) ?>
        </section>

        <div class="messages" data-chat data-item="<?= $itemId ?>">
            <p class="help">Loading messages…</p>
        </div>

        <form class="send-form" method="post" action="send_message.php">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="item_id" value="<?= $itemId ?>">
            <input required maxlength="2000" name="message" placeholder="Write a message">
            <button>Send</button>
        </form>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
