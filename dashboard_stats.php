<?php
// backend/dashboard_stats.php - Dashboard statistics for admin
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/db.php';

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    respond(['error' => 'Non autorisé'], 401);
}

// Get employees count
$employeesStmt = $conn->prepare("SELECT COUNT(*) as total FROM users WHERE role = 'employee' AND is_active = 1");
$employeesStmt->execute();
$employeesCount = $employeesStmt->get_result()->fetch_assoc()['total'];
$employeesStmt->close();

// Get requests statistics
$requestsStmt = $conn->prepare("
    SELECT 
        status,
        COUNT(*) as count
    FROM requests 
    GROUP BY status
");
$requestsStmt->execute();
$requestsResult = $requestsStmt->get_result();
$requestsStats = [];
while ($row = $requestsResult->fetch_assoc()) {
    $requestsStats[$row['status']] = intval($row['count']);
}
$requestsStmt->close();

// Get recent requests (last 5)
$recentStmt = $conn->prepare("
    SELECT r.id, r.type, r.title, r.status, r.created_at, u.firstname, u.lastname
    FROM requests r
    JOIN users u ON u.id = r.user_id
    ORDER BY r.created_at DESC
    LIMIT 5
");
$recentStmt->execute();
$recentResult = $recentStmt->get_result();
$recentRequests = [];
while ($row = $recentResult->fetch_assoc()) {
    $recentRequests[] = $row;
}
$recentStmt->close();

respond([
    'employees_count' => intval($employeesCount),
    'requests_stats' => $requestsStats,
    'recent_requests' => $recentRequests
]);
?>
