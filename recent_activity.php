<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['activities' => []]);
    exit;
}

try {
    // Récupérer les 10 dernières activités (demandes récentes, approbations, rejets)
    $sql = "
        (SELECT 
            'new' as type,
            CONCAT('Nouvelle demande de ', u.firstname, ' ', u.lastname) as description,
            r.created_at,
            TIMESTAMPDIFF(MINUTE, r.created_at, NOW()) as minutes_ago
        FROM requests r
        JOIN users u ON r.user_id = u.id
        WHERE r.created_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY r.created_at DESC
        LIMIT 5)
        
        UNION ALL
        
        (SELECT 
            'approved' as type,
            CONCAT('Demande approuvée - ', u.firstname, ' ', u.lastname) as description,
            r.updated_at as created_at,
            TIMESTAMPDIFF(MINUTE, r.updated_at, NOW()) as minutes_ago
        FROM requests r
        JOIN users u ON r.user_id = u.id
        WHERE r.status = 'approved' 
        AND r.updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY r.updated_at DESC
        LIMIT 3)
        
        UNION ALL
        
        (SELECT 
            'rejected' as type,
            CONCAT('Demande rejetée - ', u.firstname, ' ', u.lastname) as description,
            r.updated_at as created_at,
            TIMESTAMPDIFF(MINUTE, r.updated_at, NOW()) as minutes_ago
        FROM requests r
        JOIN users u ON r.user_id = u.id
        WHERE r.status = 'rejected' 
        AND r.updated_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR)
        ORDER BY r.updated_at DESC
        LIMIT 2)
        
        ORDER BY created_at DESC
        LIMIT 10
    ";
    
    $result = $conn->query($sql);
    $activities = [];
    
    while ($row = $result->fetch_assoc()) {
        $time_ago = '';
        $minutes = $row['minutes_ago'];
        
        if ($minutes < 1) {
            $time_ago = 'À l\'instant';
        } elseif ($minutes < 60) {
            $time_ago = "Il y a $minutes min";
        } elseif ($minutes < 1440) {
            $hours = floor($minutes / 60);
            $time_ago = "Il y a $hours h";
        } else {
            $days = floor($minutes / 1440);
            $time_ago = "Il y a $days j";
        }
        
        $activities[] = [
            'type' => $row['type'],
            'description' => $row['description'],
            'time_ago' => $time_ago,
            'created_at' => $row['created_at']
        ];
    }
    
    echo json_encode(['activities' => $activities]);
    
} catch (Exception $e) {
    echo json_encode(['activities' => []]);
}
?>