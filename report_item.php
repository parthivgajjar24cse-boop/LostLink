<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$error = '';
$data = [
    'item_name' => '',
    'category' => '',
    'event_date' => '',
    'event_time' => '',
    'location' => '',
    'description' => '',
    'phone' => current_user()['phone'],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    foreach ($data as $key => $_) {
        $data[$key] = trim($_POST[$key] ?? '');
    }

    $requiredFields = [
        $data['item_name'],
        $data['category'],
        $data['event_date'],
        $data['location'],
        $data['description'],
        $data['phone'],
    ];

    if (in_array('', $requiredFields, true)) {
        $error = 'Please complete all required fields.';
    } else {
        try {
            $image = safe_upload($_FILES['image'] ?? []);
            $prefix = $report_type === 'LOST' ? 'L' : 'F';

            $stmt = $pdo->prepare('SELECT COALESCE(MAX(CAST(SUBSTRING(iu_number, 2) AS UNSIGNED)), 0) + 1 FROM items WHERE type = ?');
            $stmt->execute([$report_type]);
            $iu = $prefix . str_pad((string) $stmt->fetchColumn(), 3, '0', STR_PAD_LEFT);

            $insert = $pdo->prepare(
                'INSERT INTO items (iu_number, user_id, type, item_name, category, description, location, event_date, event_time, phone, image_path) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $insert->execute([
                $iu,
                current_user()['id'],
                $report_type,
                $data['item_name'],
                $data['category'],
                $data['description'],
                $data['location'],
                $data['event_date'],
                $data['event_time'] ?: null,
                $data['phone'],
                $image,
            ]);

            $itemId = (int) $pdo->lastInsertId();

            $notify = $pdo->prepare('INSERT INTO notifications (user_id, title, message, related_item_id) VALUES (?, ?, ?, ?)');
            $notify->execute([
                current_user()['id'],
                'Item report submitted',
                $iu . ' has been added to the ' . strtolower($report_type) . ' items list.',
                $itemId,
            ]);

            set_flash('success', 'Your item report has been submitted with IU No. ' . $iu . '.');
            redirect('item.php?id=' . $itemId);
        } catch (Throwable $exception) {
            $error = $exception instanceof RuntimeException
                ? $exception->getMessage()
                : 'Unable to submit the item report.';
        }
    }
}

$active = $report_type === 'LOST' ? 'lost' : 'found';
$page_title = 'Report ' . $report_type . ' Item';

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <main class="content">
        <h1>Report <?= e($report_type) ?> Item</h1>

        <?php if ($error): ?>
            <p class="flash error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post" enctype="multipart/form-data" class="form-grid">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

            <label class="field">
                <span>Item Name</span>
                <input required name="item_name" value="<?= e($data['item_name']) ?>">
            </label>

            <label class="field">
                <span>Category</span>
                <input required name="category" placeholder="e.g. Electronics" value="<?= e($data['category']) ?>">
            </label>

            <label class="field">
                <span>Date</span>
                <input required type="date" name="event_date" value="<?= e($data['event_date']) ?>">
            </label>

            <label class="field">
                <span>Time</span>
                <input type="time" name="event_time" value="<?= e($data['event_time']) ?>">
            </label>

            <label class="field full">
                <span>Location</span>
                <input required name="location" value="<?= e($data['location']) ?>">
            </label>

            <label class="field full">
                <span>Description</span>
                <textarea required name="description" placeholder="Provide identifying details."><?= e($data['description']) ?></textarea>
            </label>

            <label class="field">
                <span>Contact Phone</span>
                <input required name="phone" value="<?= e($data['phone']) ?>">
            </label>

            <label class="field">
                <span>Photograph (optional)</span>
                <input type="file" name="image" accept="image/jpeg,image/png,image/webp">
                <small class="help">JPG, PNG or WEBP, maximum 5 MB.</small>
            </label>

            <div class="field full">
                <button>Submit Report</button>
            </div>
        </form>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
