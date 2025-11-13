<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require 'db.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

$user_id = intval($_SESSION['user_id']);

$sql = "SELECT type, title, description, start_date, end_date, status, created_at, updated_at
        FROM requests
        WHERE user_id = ?
        ORDER BY created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $user_id);
$stmt->execute();
$res = $stmt->get_result();

$history = [];
while ($row = $res->fetch_assoc()) {
    $history[] = $row;
}
$stmt->close();

echo json_encode(['history' => $history], JSON_UNESCAPED_UNICODE);