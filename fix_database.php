<?php
require 'db.php';

header('Content-Type: application/json');

$results = [];

try {
    // Vérifier et ajouter la colonne file_path à requests
    $check_column = $conn->query("SHOW COLUMNS FROM requests LIKE 'file_path'");
    if ($check_column->num_rows == 0) {
        $alter_table = $conn->query("ALTER TABLE requests ADD COLUMN file_path VARCHAR(500) NULL AFTER description");
        if ($alter_table) {
            $results[] = "✅ Colonne file_path ajoutée à la table requests";
        } else {
            $results[] = "❌ Erreur lors de l'ajout de file_path: " . $conn->error;
        }
    } else {
        $results[] = "✅ Colonne file_path existe déjà";
    }

    // Vérifier et créer la table documents
    $check_table = $conn->query("SHOW TABLES LIKE 'documents'");
    if ($check_table->num_rows == 0) {
        $create_table = $conn->query("
            CREATE TABLE documents (
                id INT AUTO_INCREMENT PRIMARY KEY,
                request_id INT NOT NULL,
                file_path VARCHAR(500) NOT NULL,
                file_name VARCHAR(255) NOT NULL,
                uploaded_at DATETIME NOT NULL,
                FOREIGN KEY (request_id) REFERENCES requests(id) ON DELETE CASCADE,
                UNIQUE KEY unique_request_doc (request_id)
            )
        ");
        if ($create_table) {
            $results[] = "✅ Table documents créée";
        } else {
            $results[] = "❌ Erreur création table documents: " . $conn->error;
        }
    } else {
        $results[] = "✅ Table documents existe déjà";
    }

    // Vérifier et créer la table notifications
    $check_notif = $conn->query("SHOW TABLES LIKE 'notifications'");
    if ($check_notif->num_rows == 0) {
        $create_notif = $conn->query("
            CREATE TABLE notifications (
                id INT AUTO_INCREMENT PRIMARY KEY,
                user_id INT NOT NULL,
                title VARCHAR(255) NOT NULL,
                message TEXT NOT NULL,
                is_read TINYINT(1) DEFAULT 0,
                created_at DATETIME NOT NULL,
                FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
            )
        ");
        if ($create_notif) {
            $results[] = "✅ Table notifications créée";
        } else {
            $results[] = "❌ Erreur création table notifications: " . $conn->error;
        }
    } else {
        $results[] = "✅ Table notifications existe déjà";
    }

    echo json_encode([
        'success' => true,
        'results' => $results
    ]);

} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'results' => $results
    ]);
}
?>