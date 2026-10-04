<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();
    $identity = trim($_POST['identity'] ?? '');
    $password = $_POST['password'] ?? '';

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? OR enrollment_no = ? LIMIT 1');
    $stmt->execute([$identity, $identity]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        unset($user['password']);
        $_SESSION['user'] = $user;
        redirect('dashboard.php');
    }

    $error = 'Invalid email/enrollment number or password.';
}

$page_title = 'Login';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-box">
    <h1>Log In</h1>

    <?php if ($error): ?>
        <p class="flash error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <label class="field full">
            <span>University Email or Enrollment Number</span>
            <input required name="identity" value="<?= e($_POST['identity'] ?? '') ?>">
        </label>

        <label class="field full">
            <span>Password</span>
            <input required type="password" name="password">
        </label>

        <div class="field full">
            <button>Log In</button>
            <p>Need an account? <a href="register.php">Register</a></p>
        </div>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
