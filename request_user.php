<?php
// backend/request_user.php - Employee requests management
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
    // Get user's requests with optional status filter
    $status = isset($_GET['status']) ? $_GET['status'] : '';
    $rows = [];
    if ($status && in_array($status, ['pending','approved','rejected'], true)) {
        $stmt = $conn->prepare("SELECT id, type, title, description, start_date, end_date, comments, status, created_at, updated_at FROM requests WHERE user_id = ? AND status = ? ORDER BY created_at DESC");
        $stmt->bind_param('is', $userId, $status);
    } else {
        $stmt = $conn->prepare("SELECT id, type, title, description, start_date, end_date, comments, status, created_at, updated_at FROM requests WHERE user_id = ? ORDER BY created_at DESC");
        $stmt->bind_param('i', $userId);
    }
    if ($stmt && $stmt->execute()) {
        $stmt->bind_result($id, $type, $title, $description, $start_date, $end_date, $comments, $statusVal, $created_at, $updated_at);
        while ($stmt->fetch()) {
            $rows[] = [
                'id' => $id,
                'type' => $type,
                'title' => $title,
                'description' => $description,
                'start_date' => $start_date,
                'end_date' => $end_date,
                'comments' => $comments,
                'status' => $statusVal,
                'created_at' => $created_at,
                'updated_at' => $updated_at
            ];
        }
        $stmt->close();
    }

    // Get statistics
    $stats = [];
    $statsStmt = $conn->prepare("SELECT status, COUNT(*) as count FROM requests WHERE user_id = ? GROUP BY status");
    $statsStmt->bind_param('i', $userId);
    if ($statsStmt && $statsStmt->execute()) {
        $statsStmt->bind_result($s, $cnt);
        while ($statsStmt->fetch()) {
            $stats[$s] = intval($cnt);
        }
        $statsStmt->close();
    }

    respond([
        'requests' => $rows,
        'stats' => $stats
    ]);
}

respond(['error' => 'Méthode non autorisée'], 405);
?>