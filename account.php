<?php

require_once __DIR__ . '/includes/auth.php';
require_login();
require_once __DIR__ . '/config/database.php';

$active = 'account';
$page_title = 'Account';
$error = '';
$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $action = $_POST['action'] ?? '';

    if ($action === 'profile') {
        foreach (['username', 'department', 'semester', 'batch', 'phone'] as $f) {
            $user[$f] = trim($_POST[$f] ?? '');
        }

        $requiredProfileFields = [
            $user['username'],
            $user['department'],
            $user['semester'],
            $user['batch'],
            $user['phone'],
        ];

        if (in_array('', $requiredProfileFields, true)) {
            $error = 'Please complete all profile fields.';
        } else {
            $stmt = $pdo->prepare('UPDATE users SET username = ?, department = ?, semester = ?, batch = ?, phone = ? WHERE id = ?');
            $stmt->execute([
                $user['username'],
                $user['department'],
                $user['semester'],
                $user['batch'],
                $user['phone'],
                $user['id'],
            ]);

            $_SESSION['user'] = $user;
            set_flash('success', 'Profile updated.');
            redirect('account.php');
        }
    } elseif ($action === 'password') {
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$user['id']]);
        $hash = (string) $stmt->fetchColumn();

        if (!password_verify($_POST['current_password'] ?? '', $hash)) {
            $error = 'Current password is incorrect.';
        }

        if (!$error) {
            $new = $_POST['new_password'] ?? '';
            if (strlen($new) < 8) {
                $error = 'New password must have at least 8 characters.';
            } else {
                $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                    ->execute([password_hash($new, PASSWORD_DEFAULT), $user['id']]);

                set_flash('success', 'Password changed.');
                redirect('account.php');
            }
        }
    }
}

require __DIR__ . '/includes/header.php';
?>
<div class="page-shell">
    <?php require __DIR__ . '/includes/sidebar.php'; ?>

    <main class="content">
        <h1>Account</h1>

        <?php if ($error): ?>
            <p class="flash error"><?= e($error) ?></p>
        <?php endif; ?>

        <form method="post" class="form-grid">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="profile">

            <label class="field">
                <span>Username</span>
                <input required name="username" value="<?= e($user['username']) ?>">
            </label>

            <label class="field">
                <span>Email</span>
                <input disabled value="<?= e($user['email']) ?>">
            </label>

            <label class="field">
                <span>Department</span>
                <input required name="department" value="<?= e($user['department']) ?>">
            </label>

            <label class="field">
                <span>Semester</span>
                <input required name="semester" value="<?= e($user['semester']) ?>">
            </label>

            <label class="field">
                <span>Batch</span>
                <input required name="batch" value="<?= e($user['batch']) ?>">
            </label>

            <label class="field">
                <span>Enrollment Number</span>
                <input disabled value="<?= e($user['enrollment_no']) ?>">
            </label>

            <label class="field">
                <span>Phone Number</span>
                <input required name="phone" value="<?= e($user['phone']) ?>">
            </label>

            <div class="field full">
                <button>Save Profile</button>
            </div>
        </form>

        <hr>

        <h2>Change Password</h2>

        <form method="post" class="form-grid">
            <input type="hidden" name="csrf" value="<?= csrf_token() ?>">
            <input type="hidden" name="action" value="password">

            <label class="field">
                <span>Current Password</span>
                <input required type="password" name="current_password">
            </label>

            <label class="field">
                <span>New Password</span>
                <input required type="password" name="new_password" minlength="8">
            </label>

            <div class="field full">
                <button>Change Password</button>
                <a class="button secondary" href="logout.php">Log Out</a>
            </div>
        </form>
    </main>
</div>
<?php require __DIR__ . '/includes/footer.php'; ?>
