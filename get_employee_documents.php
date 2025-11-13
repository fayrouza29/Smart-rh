<?php
session_start();
require 'db.php';

header('Content-Type: application/json');

$response = [
    'success' => false,
    'message' => '',
    'documents' => []
];

// Vérifier que l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Non connecté';
    echo json_encode($response);
    exit;
}

$user_id = $_SESSION['user_id'];

try {
    // Vérifier si la table documents existe
    $table_check = $conn->query("SHOW TABLES LIKE 'documents'");
    if ($table_check->num_rows == 0) {
        $response['message'] = 'Aucun document disponible pour le moment';
        $response['success'] = true;
        echo json_encode($response);
        exit;
    }

    // Requête pour récupérer les documents de l'employé
    $sql = "
        SELECT 
            d.file_path,
            d.file_name,
            d.uploaded_at,
            r.title,
            r.type,
            r.status
        FROM documents d
        INNER JOIN requests r ON d.request_id = r.id
        WHERE r.user_id = ? 
        AND r.status = 'approved'
        ORDER BY d.uploaded_at DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $documents = [];
    while ($row = $result->fetch_assoc()) {
        // Vérifier que le fichier existe physiquement
        $file_path = $row['file_path'];
        $full_path = __DIR__ . '/../' . $file_path;
        
        if (file_exists($full_path)) {
            $documents[] = [
                'file_path' => $file_path,
                'file_name' => $row['file_name'],
                'title' => $row['title'],
                'type' => $row['type'],
                'uploaded_at' => date('d/m/Y H:i', strtotime($row['uploaded_at'])),
                'file_size' => filesize($full_path),
                'status' => $row['status']
            ];
        } else {
            error_log("Document introuvable: " . $full_path);
        }
    }

    $response['success'] = true;
    $response['documents'] = $documents;
    $response['count'] = count($documents);
    
    if (empty($documents)) {
        $response['message'] = 'Aucun document disponible';
    }

} catch (Exception $e) {
    $response['message'] = 'Erreur lors du chargement des documents: ' . $e->getMessage();
    error_log("Erreur get_employee_documents: " . $e->getMessage());
}

echo json_encode($response);
?>