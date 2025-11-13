<?php
// backend/user_notifications.php - Fetch and mark notifications for logged-in employee
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if (!isset($_SESSION['user_id'])) {
    respond(['error' => 'Non autorisé'], 401);
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = intval($_SESSION['user_id']);

if ($method === 'GET') {
    $stmt = $conn->prepare("SELECT id, title, message, is_read, created_at FROM notifications WHERE user_id = ? AND is_read = 0 ORDER BY created_at DESC");
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $res = $stmt->get_result();
    $rows = [];
    while ($row = $res->fetch_assoc()) { $rows[] = $row; }
    $stmt->close();
    respond(['notifications' => $rows]);
}

if ($method === 'POST') {
    // mark all as read
    $upd = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE user_id = ? AND is_read = 0");
    $upd->bind_param('i', $userId);
    $upd->execute();
    $upd->close();
    respond(['success' => true]);
}

respond(['error' => 'Méthode non autorisée'], 405);
?>



