<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirect('dashboard.php');
}

verify_csrf();

$itemId = filter_input(INPUT_POST, 'item_id', FILTER_VALIDATE_INT);
$text = trim($_POST['message'] ?? '');

if (!$itemId || $text === '') {
    set_flash('error', 'A message is required.');
    redirect('dashboard.php');
}

$stmt = $pdo->prepare('SELECT user_id, iu_number FROM items WHERE id = ?');
$stmt->execute([$itemId]);
$item = $stmt->fetch();

if (!$item || $item['user_id'] == current_user()['id']) {
    set_flash('error', 'Message could not be sent.');
    redirect('dashboard.php');
}

$pdo->prepare('INSERT INTO messages (item_id, sender_id, receiver_id, message) VALUES (?, ?, ?, ?)')
    ->execute([$itemId, current_user()['id'], $item['user_id'], $text]);

$pdo->prepare('INSERT INTO notifications (user_id, title, message, related_item_id) VALUES (?, ?, ?, ?)')
    ->execute([$item['user_id'], 'New message', 'You received a message regarding ' . $item['iu_number'] . '.', $itemId]);

redirect('chat.php?item_id=' . $itemId);
