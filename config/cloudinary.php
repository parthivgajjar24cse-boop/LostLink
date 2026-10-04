<?php
declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use Cloudinary\Configuration\Configuration;
use Cloudinary\Api\Upload\UploadApi;

// Initialize Cloudinary using environment variables with getenv fallback
$cloudName = $_ENV['CLOUDINARY_CLOUD_NAME'] ?? getenv('CLOUDINARY_CLOUD_NAME') ?: '';
$apiKey = $_ENV['CLOUDINARY_API_KEY'] ?? getenv('CLOUDINARY_API_KEY') ?: '';
$apiSecret = $_ENV['CLOUDINARY_API_SECRET'] ?? getenv('CLOUDINARY_API_SECRET') ?: '';

if ($cloudName && $apiKey && $apiSecret) {
    Configuration::instance([
        'cloud' => [
            'cloud_name' => $cloudName,
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
        ],
        'url' => [
            'secure' => true
        ]
    ]);
}

/**
 * Uploads a local file to Cloudinary and returns the secure HTTPS URL.
 * Returns null if upload fails or credentials are missing.
 *
 * @param string $fileTmpPath
 * @return string|null
 */
function uploadToCloudinary(string $fileTmpPath): ?string
{
    try {
        $uploadApi = new UploadApi();
        $response = $uploadApi->upload($fileTmpPath, [
            'folder' => 'lostlink_uploads',
            'resource_type' => 'image'
        ]);
        return $response['secure_url'] ?? null;
    } catch (\Throwable $e) {
        error_log('Cloudinary Upload Error: ' . $e->getMessage());
        return null;
    }
}