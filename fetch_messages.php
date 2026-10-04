<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

header('Content-Type: text/html; charset=utf-8');

$itemId = filter_input(INPUT_GET, 'item_id', FILTER_VALIDATE_INT);
if (!$itemId) {
    exit;
}

$owner = $pdo->prepare('SELECT user_id FROM items WHERE id = ?');
$owner->execute([$itemId]);
$reporter = (int) $owner->fetchColumn();

if (!$reporter || $reporter === current_user()['id']) {
    exit;
}

$stmt = $pdo->prepare(
    'SELECT * FROM messages 
     WHERE item_id = ? 
       AND ((sender_id = ? AND receiver_id = ?) 
        OR (sender_id = ? AND receiver_id = ?)) 
     ORDER BY created_at'
);
$stmt->execute([$itemId, current_user()['id'], $reporter, $reporter, current_user()['id']]);
$messages = $stmt->fetchAll();

$pdo->prepare('UPDATE messages SET is_read = 1 WHERE item_id = ? AND receiver_id = ?')
    ->execute([$itemId, current_user()['id']]);

if (!$messages) {
    echo '<p class="help">No messages yet. Start the conversation.</p>';
    exit;
}

foreach ($messages as $message) {
    $mine = (int) $message['sender_id'] === (int) current_user()['id'];
    ?>
    <div class="message <?= $mine ? 'mine' : '' ?>">
        <?= nl2br(e($message['message'])) ?>
        <small><?= e(date('d/m/Y H:i', strtotime($message['created_at']))) ?></small>
    </div>
    <?php
}
