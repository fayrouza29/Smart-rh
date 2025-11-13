<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$checks = [
    'php_version' => PHP_VERSION,
    'max_file_uploads' => ini_get('max_file_uploads'),
    'upload_max_filesize' => ini_get('upload_max_filesize'),
    'post_max_size' => ini_get('post_max_size'),
    'memory_limit' => ini_get('memory_limit'),
    'max_execution_time' => ini_get('max_execution_time'),
    'session_active' => isset($_SESSION['user_id']),
    'user_role' => $_SESSION['role'] ?? 'none',
    'uploads_dir_exists' => file_exists(__DIR__ . '/../uploads/'),
    'uploads_dir_writable' => is_writable(__DIR__ . '/../uploads/'),
    'temp_dir_writable' => is_writable(sys_get_temp_dir()),
];

// Test d'écriture dans le dossier uploads
$test_file = __DIR__ . '/../uploads/test_write.txt';
if (file_put_contents($test_file, 'test') !== false) {
    $checks['test_write'] = 'OK';
    unlink($test_file);
} else {
    $checks['test_write'] = 'FAILED';
}

echo json_encode(['checks' => $checks]);
?>