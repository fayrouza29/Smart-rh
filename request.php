<?php
// backend/request.php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    $type = isset($_POST['type']) ? trim($_POST['type']) : '';
    $title = isset($_POST['title']) ? trim($_POST['title']) : '';
    $description = isset($_POST['description']) ? trim($_POST['description']) : '';
    $start_date = $_POST['start_date'] ?? null;
    $end_date = $_POST['end_date'] ?? null;
    $comments = isset($_POST['comments']) ? trim($_POST['comments']) : '';

    // Basic validation
    $allowedTypes = ['leave','remote','training','document','equipment'];
    if ($type === '' || !in_array($type, $allowedTypes, true)) {
        header("Location: ../employer.php?error=" . urlencode("Type de demande invalide"));
        exit();
    }
    if ($title === '') {
        // Fallback title by type
        $title = 'Demande ' . $type;
    }
    
    $sql = "INSERT INTO requests (user_id, type, title, description, start_date, end_date, comments, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("issssss", $user_id, $type, $title, $description, $start_date, $end_date, $comments);
    
    if ($stmt->execute()) {
        $request_id = $conn->insert_id;
        
        // Notify all admins
        $fullName = isset($_SESSION['firstname']) && isset($_SESSION['lastname']) ? (trim($_SESSION['firstname'] . ' ' . $_SESSION['lastname'])) : 'Employé';
        $notif_title = 'Nouvelle demande';
        $notif_msg = $fullName . ' a soumis une demande: ' . $title;
        
        $admins = $conn->prepare("SELECT id FROM users WHERE role = 'admin' AND is_active = 1");
        $admins->execute();
        $res = $admins->get_result();
        while ($row = $res->fetch_assoc()) {
            $ins = $conn->prepare("INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
            $ins->bind_param('iss', $row['id'], $notif_title, $notif_msg);
            $ins->execute();
            $ins->close();
        }
        $admins->close();
        
        // Confirmation notification to employee
        $confirmation_title = 'Demande soumise';
        $confirmation_msg = 'Votre demande "' . $title . '" a été soumise avec succès et est en cours de traitement.';
        $confirmation = $conn->prepare("INSERT INTO notifications (user_id, title, message, is_read, created_at) VALUES (?, ?, ?, 0, NOW())");
        $confirmation->bind_param('iss', $user_id, $confirmation_title, $confirmation_msg);
        $confirmation->execute();
        $confirmation->close();
        
        header("Location: ../employer.php?success=Demande envoyée avec succès");
    } else {
        $err = $conn->error;
        if (empty($err) && method_exists($stmt, 'error')) { $err = $stmt->error; }
        header("Location: ../employer.php?error=" . urlencode("Erreur lors de l'envoi de la demande: " . $err));
    }
    
    $stmt->close();
    $conn->close();
} else {
    header("Location: ../log.html");
    exit();
}
?>