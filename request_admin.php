<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

// Vérifier les permissions
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    // Récupérer les demandes avec informations sur les documents
    $whereConditions = [];
    $params = [];
    $types = '';
    
    if (isset($_GET['status']) && !empty($_GET['status'])) {
        $whereConditions[] = "r.status = ?";
        $params[] = $_GET['status'];
        $types .= 's';
    }
    
    if (isset($_GET['type']) && !empty($_GET['type'])) {
        $whereConditions[] = "r.type = ?";
        $params[] = $_GET['type'];
        $types .= 's';
    }
    
    $whereClause = '';
    if (!empty($whereConditions)) {
        $whereClause = 'WHERE ' . implode(' AND ', $whereConditions);
    }
    
    // Vérifier si la colonne file_path existe
    $column_check = $conn->query("SHOW COLUMNS FROM requests LIKE 'file_path'");
    $has_file_path = $column_check->num_rows > 0;
    
    $file_path_select = $has_file_path ? ', r.file_path' : ', NULL as file_path';
    
    // Vérifier si la table documents existe
    $table_check = $conn->query("SHOW TABLES LIKE 'documents'");
    $has_documents = $table_check->num_rows > 0;
    
    $doc_count_select = $has_documents ? ', (SELECT COUNT(*) FROM documents d WHERE d.request_id = r.id) as doc_count' : ', 0 as doc_count';
    
    $sql = "
        SELECT 
            r.*, 
            u.firstname, 
            u.lastname, 
            u.email 
            {$file_path_select}
            {$doc_count_select}
        FROM requests r 
        JOIN users u ON r.user_id = u.id 
        {$whereClause}
        ORDER BY r.created_at DESC
    ";
    
    $stmt = $conn->prepare($sql);
    
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    
    $stmt->execute();
    $result = $stmt->get_result();
    $requests = $result->fetch_all(MYSQLI_ASSOC);
    
    echo json_encode(['requests' => $requests]);
    
} elseif ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    $request_id = intval($_POST['id'] ?? 0);
    
    if ($action === 'approve' || $action === 'reject') {
        $status = $action === 'approve' ? 'approved' : 'rejected';
        $stmt = $conn->prepare("UPDATE requests SET status = ? WHERE id = ?");
        $stmt->bind_param("si", $status, $request_id);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['error' => 'Erreur lors de la mise à jour']);
        }
    } else {
        echo json_encode(['error' => 'Action non reconnue']);
    }
}
?>