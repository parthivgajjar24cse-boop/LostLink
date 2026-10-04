<?php

declare(strict_types=1);

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

function redirect(string $path): never
{
    header('Location: ' . $path);
    exit;
}

function set_flash(string $type, string $message): void
{
    $_SESSION['flash'] = [
        'type' => $type,
        'message' => $message,
    ];
}

function get_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function csrf_token(): string
{
    $_SESSION['csrf'] ??= bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}

function verify_csrf(): void
{
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        http_response_code(403);
        exit('Invalid form request. Please return and try again.');
    }
}

function current_user(): ?array
{
    return $_SESSION['user'] ?? null;
}

function is_logged_in(): bool
{
    return isset($_SESSION['user']['id']);
}

function require_login(): void
{
    if (!is_logged_in()) {
        set_flash('error', 'Please log in or register to access that feature.');
        redirect('login.php');
    }
}

function item_class(string $type): string
{
    return strtoupper($type) === 'LOST' ? 'lost' : 'found';
}

function format_date(?string $date): string
{
    return $date ? date('d/m/Y', strtotime($date)) : '—';
}

function safe_upload(array $file): ?string
{
    if (($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    if (($file['error'] ?? UPLOAD_ERR_OK) !== UPLOAD_ERR_OK || $file['size'] > 5 * 1024 * 1024) {
        throw new RuntimeException('Image upload failed or is larger than 5 MB.');
    }

    $finfo = new finfo(FILEINFO_MIME_TYPE);
    $mime = $finfo->file($file['tmp_name']);

    $extensions = [
        'image/jpeg' => 'jpg',
        'image/png' => 'png',
        'image/webp' => 'webp',
    ];

    if (!isset($extensions[$mime])) {
        throw new RuntimeException('Only JPG, PNG, and WEBP images are accepted.');
    }

    $name = bin2hex(random_bytes(16)) . '.' . $extensions[$mime];
    $directory = __DIR__ . '/../uploads/items';

    if (!is_dir($directory) && !mkdir($directory, 0755, true)) {
        throw new RuntimeException('Unable to prepare upload folder.');
    }

    if (!move_uploaded_file($file['tmp_name'], $directory . '/' . $name)) {
        throw new RuntimeException('Unable to save the uploaded image.');
    }

    return 'uploads/items/' . $name;
}
