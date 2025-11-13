<?php
// backend/user_documents.php - List user documents
session_start();
require_once __DIR__ . '/db.php';

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$method = $_SERVER['REQUEST_METHOD'];
$userId = intval($_SESSION['user_id']);

if ($method === 'GET') {
    header('Content-Type: application/json; charset=utf-8');
    // Ensure documents table exists; if not, return empty
    $hasDocs = false;
    $chk = $conn->query("SHOW TABLES LIKE 'documents'");
    if ($chk) { $hasDocs = $chk->num_rows > 0; $chk->close(); }
    if (!$hasDocs) {
        echo json_encode(['documents' => []], JSON_UNESCAPED_UNICODE);
        exit;
    }
    $stmt = $conn->prepare("SELECT d.id, d.title, d.file_name, d.file_path, d.created_at, r.type AS request_type
                            FROM documents d
                            LEFT JOIN requests r ON r.id = d.request_id
                            WHERE d.user_id = ? ORDER BY d.created_at DESC");
    $stmt->bind_param('i', $userId);
    if ($stmt->execute()) {
        $stmt->bind_result($id, $title, $file_name, $file_path, $created_at, $request_type);
        $rows = [];
        while ($stmt->fetch()) {
            $rows[] = [
                'id' => $id,
                'title' => $title,
                'file_name' => $file_name,
                'file_path' => $file_path,
                'created_at' => $created_at,
                'request_type' => $request_type
            ];
        }
        $stmt->close();
        echo json_encode(['documents' => $rows], JSON_UNESCAPED_UNICODE);
    } else {
        $err = $stmt->error;
        $stmt->close();
        echo json_encode(['documents' => [], 'error' => 'SQL exec error', 'detail' => $err], JSON_UNESCAPED_UNICODE);
    }
    exit;
}

http_response_code(405);
header('Content-Type: application/json; charset=utf-8');
echo json_encode(['error' => 'Méthode non autorisée']);
?>


