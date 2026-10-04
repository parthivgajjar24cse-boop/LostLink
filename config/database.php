<?php
declare(strict_types=1);

// Read database credentials from environment variables (Railway support) or fall back to defaults
$dbHost = $_ENV['MYSQLHOST'] ?? $_ENV['DB_HOST'] ?? getenv('MYSQLHOST') ?: (getenv('DB_HOST') ?: 'localhost');
$dbPort = $_ENV['MYSQLPORT'] ?? $_ENV['DB_PORT'] ?? getenv('MYSQLPORT') ?: (getenv('DB_PORT') ?: '3306');
$dbName = $_ENV['MYSQLDATABASE'] ?? $_ENV['DB_NAME'] ?? getenv('MYSQLDATABASE') ?: (getenv('DB_NAME') ?: 'lostlink');
$dbUser = $_ENV['MYSQLUSER'] ?? $_ENV['DB_USER'] ?? getenv('MYSQLUSER') ?: (getenv('DB_USER') ?: 'root');
$dbPass = $_ENV['MYSQLPASSWORD'] ?? $_ENV['DB_PASS'] ?? getenv('MYSQLPASSWORD') ?: (getenv('DB_PASS') ?: '');

try {
    $dsn = "mysql:host={$dbHost};port={$dbPort};dbname={$dbName};charset=utf8mb4";
    $pdo = new PDO($dsn, $dbUser, $dbPass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}