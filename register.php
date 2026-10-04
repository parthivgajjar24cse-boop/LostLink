<?php

require_once __DIR__ . '/includes/auth.php';
require_once __DIR__ . '/config/database.php';

if (is_logged_in()) {
    redirect('dashboard.php');
}

$values = [
    'username' => '',
    'email' => '',
    'department' => '',
    'semester' => '',
    'batch' => '',
    'enrollment_no' => '',
    'phone' => '',
];
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verify_csrf();

    foreach ($values as $key => $_) {
        $values[$key] = trim($_POST[$key] ?? '');
    }

    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (in_array('', $values, true) || $password === '') {
        $error = 'Please complete every required field.';
    } elseif (!filter_var($values['email'], FILTER_VALIDATE_EMAIL)) {
        $error = 'Enter a valid email address.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must have at least 8 characters.';
    } elseif ($password !== $confirm) {
        $error = 'Password confirmation does not match.';
    } else {
        try {
            $statement = $pdo->prepare(
                'INSERT INTO users (username, email, password, department, semester, batch, enrollment_no, phone) 
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?)'
            );
            $statement->execute([
                $values['username'],
                $values['email'],
                password_hash($password, PASSWORD_DEFAULT),
                $values['department'],
                $values['semester'],
                $values['batch'],
                $values['enrollment_no'],
                $values['phone'],
            ]);

            set_flash('success', 'Account created. Please log in.');
            redirect('login.php');
        } catch (PDOException $e) {
            $error = $e->getCode() === '23000'
                ? 'Email or enrollment number is already registered.'
                : 'Unable to create account.';
        }
    }
}

$page_title = 'Register';
require __DIR__ . '/includes/header.php';
?>
<main class="auth-box">
    <h1>Create Account</h1>

    <?php if ($error): ?>
        <p class="flash error"><?= e($error) ?></p>
    <?php endif; ?>

    <form method="post" class="form-grid">
        <input type="hidden" name="csrf" value="<?= csrf_token() ?>">

        <?php
        $fields = [
            'username' => 'Username',
            'email' => 'University Email',
            'department' => 'Department',
            'semester' => 'Semester',
            'batch' => 'Batch',
            'enrollment_no' => 'Enrollment Number',
            'phone' => 'Phone Number',
        ];
        ?>
        <?php foreach ($fields as $name => $label): ?>
            <label class="field">
                <span><?= $label ?></span>
                <input required name="<?= $name ?>" <?= $name === 'email' ? 'type="email"' : '' ?> value="<?= e($values[$name]) ?>">
            </label>
        <?php endforeach; ?>

        <label class="field">
            <span>Password</span>
            <input required type="password" name="password" minlength="8">
        </label>

        <label class="field">
            <span>Confirm Password</span>
            <input required type="password" name="confirm_password" minlength="8">
        </label>

        <div class="field full">
            <button>Create Account</button>
            <p>Already registered? <a href="login.php">Log in</a></p>
        </div>
    </form>
</main>
<?php require __DIR__ . '/includes/footer.php'; ?>
