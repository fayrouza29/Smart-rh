<?php
session_start();
require 'db.php';

// Headers pour permettre les requêtes cross-origin et JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

$response = ['success' => false, 'message' => ''];

// Activer l'affichage des erreurs pour le debug
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Vérifier que l'utilisateur est admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    $response['message'] = 'Non autorisé - Session invalide';
    echo json_encode($response);
    exit;
}

// Vérifier la méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    $response['message'] = 'Méthode non autorisée';
    echo json_encode($response);
    exit;
}

// Vérifier si les données sont présentes
if (!isset($_FILES['document']) || !isset($_POST['request_id'])) {
    $response['message'] = 'Données manquantes - Fichier ou request_id manquant';
    $response['debug'] = [
        'files' => $_FILES,
        'post' => $_POST
    ];
    echo json_encode($response);
    exit;
}

$request_id = intval($_POST['request_id']);
$file = $_FILES['document'];

// Vérifications de base du fichier
if ($file['error'] !== UPLOAD_ERR_OK) {
    $upload_errors = [
        UPLOAD_ERR_INI_SIZE => 'Fichier trop volumineux (php.ini)',
        UPLOAD_ERR_FORM_SIZE => 'Fichier trop volumineux (form)',
        UPLOAD_ERR_PARTIAL => 'Upload partiel',
        UPLOAD_ERR_NO_FILE => 'Aucun fichier',
        UPLOAD_ERR_NO_TMP_DIR => 'Dossier temp manquant',
        UPLOAD_ERR_CANT_WRITE => 'Erreur d\'écriture',
        UPLOAD_ERR_EXTENSION => 'Extension PHP bloquée'
    ];
    $response['message'] = 'Erreur upload: ' . ($upload_errors[$file['error']] ?? 'Erreur inconnue');
    echo json_encode($response);
    exit;
}

// Vérifier que la demande existe
try {
    $check_stmt = $conn->prepare("
        SELECT r.*, u.email, u.firstname, u.lastname, u.id as user_id 
        FROM requests r 
        JOIN users u ON r.user_id = u.id 
        WHERE r.id = ? AND r.status = 'approved' AND r.type = 'document'
    ");
    $check_stmt->bind_param("i", $request_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        $response['message'] = 'Demande non trouvée ou non approuvée';
        echo json_encode($response);
        exit;
    }

    $request_data = $result->fetch_assoc();
    $user_id = $request_data['user_id'];
} catch (Exception $e) {
    $response['message'] = 'Erreur vérification demande: ' . $e->getMessage();
    echo json_encode($response);
    exit;
}

// Validation du type de fichier
$allowed_types = ['application/pdf'];
$file_type = mime_content_type($file['tmp_name']);

if (!in_array($file_type, $allowed_types)) {
    $response['message'] = 'Type de fichier non autorisé. Seuls les PDF sont acceptés. Type détecté: ' . $file_type;
    echo json_encode($response);
    exit;
}

// Validation de la taille (10MB max)
$max_size = 10 * 1024 * 1024;
if ($file['size'] > $max_size) {
    $response['message'] = 'Fichier trop volumineux. Maximum 10MB autorisé.';
    echo json_encode($response);
    exit;
}

// Créer le répertoire uploads
$uploadDir = __DIR__ . '/../uploads/';
if (!file_exists($uploadDir)) {
    if (!mkdir($uploadDir, 0755, true)) {
        $response['message'] = 'Impossible de créer le dossier uploads';
        echo json_encode($response);
        exit;
    }
}

// Vérifier que le dossier est accessible en écriture
if (!is_writable($uploadDir)) {
    $response['message'] = 'Le dossier uploads n\'est pas accessible en écriture';
    echo json_encode($response);
    exit;
}

// Générer un nom de fichier unique
$file_extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$fileName = 'doc_' . $request_id . '_' . uniqid() . '.' . $file_extension;
$filePath = $uploadDir . $fileName;
$relativePath = 'uploads/' . $fileName;

// Déplacer le fichier uploadé
if (move_uploaded_file($file['tmp_name'], $filePath)) {
    
    // Vérifier que le fichier a bien été déplacé
    if (!file_exists($filePath)) {
        $response['message'] = 'Erreur: fichier non trouvé après déplacement';
        echo json_encode($response);
        exit;
    }
    
    try {
        // Commencer une transaction
        $conn->begin_transaction();
        
        // Vérifier si la table documents existe, sinon la créer
        $table_check = $conn->query("SHOW TABLES LIKE 'documents'");
        if ($table_check->num_rows == 0) {
            // Créer la table documents
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
            if (!$create_table) {
                throw new Exception('Erreur création table documents: ' . $conn->error);
            }
        }
        
        // Vérifier si la colonne file_path existe dans requests, sinon l'ajouter
        $column_check = $conn->query("SHOW COLUMNS FROM requests LIKE 'file_path'");
        if ($column_check->num_rows == 0) {
            $add_column = $conn->query("ALTER TABLE requests ADD COLUMN file_path VARCHAR(500) NULL AFTER description");
            if (!$add_column) {
                throw new Exception('Erreur ajout colonne file_path: ' . $conn->error);
            }
        }
        
        // Vérifier si un document existe déjà pour cette demande
        $check_doc = $conn->prepare("SELECT id FROM documents WHERE request_id = ?");
        $check_doc->bind_param("i", $request_id);
        $check_doc->execute();
        $doc_result = $check_doc->get_result();
        
        if ($doc_result->num_rows > 0) {
            // Mise à jour du document existant
            $stmt = $conn->prepare("
                UPDATE documents 
                SET file_path = ?, file_name = ?, uploaded_at = NOW() 
                WHERE request_id = ?
            ");
            $stmt->bind_param("ssi", $relativePath, $fileName, $request_id);
        } else {
            // Insertion d'un nouveau document
            $stmt = $conn->prepare("
                INSERT INTO documents (request_id, file_path, file_name, uploaded_at) 
                VALUES (?, ?, ?, NOW())
            ");
            $stmt->bind_param("iss", $request_id, $relativePath, $fileName);
        }
        
        if (!$stmt->execute()) {
            throw new Exception('Erreur SQL documents: ' . $stmt->error);
        }
        $stmt->close();
        
        // Mettre à jour la table requests avec le file_path
        $update_request = $conn->prepare("UPDATE requests SET file_path = ? WHERE id = ?");
        $update_request->bind_param("si", $relativePath, $request_id);
        if (!$update_request->execute()) {
            // Si la colonne n'existe toujours pas, on ignore cette erreur
            error_log("Warning: Impossible de mettre à jour file_path dans requests: " . $update_request->error);
        }
        $update_request->close();
        
        // Vérifier si la table notifications existe
        $notif_table_check = $conn->query("SHOW TABLES LIKE 'notifications'");
        if ($notif_table_check->num_rows > 0) {
            // Créer une notification
            $notification_msg = "Votre document '{$request_data['title']}' a été téléversé par l'administrateur.";
            $notif_stmt = $conn->prepare("
                INSERT INTO notifications (user_id, title, message, created_at) 
                VALUES (?, 'Document disponible', ?, NOW())
            ");
            $notif_stmt->bind_param("is", $user_id, $notification_msg);
            if (!$notif_stmt->execute()) {
                error_log("Warning: Impossible de créer la notification: " . $notif_stmt->error);
            }
            $notif_stmt->close();
        }
        
        // Valider la transaction
        $conn->commit();
        
        $response['success'] = true;
        $response['message'] = 'PDF téléversé avec succès';
        $response['file_path'] = $relativePath;
        $response['debug'] = [
            'file_size' => $file['size'],
            'file_type' => $file_type,
            'saved_path' => $filePath
        ];
        
    } catch (Exception $e) {
        // Annuler la transaction en cas d'erreur
        $conn->rollback();
        
        // Supprimer le fichier uploadé
        if (file_exists($filePath)) {
            unlink($filePath);
        }
        
        $response['message'] = 'Erreur base de données: ' . $e->getMessage();
        $response['debug_sql'] = $conn->error;
    }
    
} else {
    $response['message'] = 'Erreur lors du déplacement du fichier';
    $response['debug'] = [
        'tmp_name' => $file['tmp_name'],
        'destination' => $filePath,
        'upload_dir_writable' => is_writable($uploadDir)
    ];
}

echo json_encode($response);
?>