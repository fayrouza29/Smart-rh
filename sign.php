<?php
// backend/sign.php
session_start();
include "db.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstname = trim($_POST['firstname']);
    $lastname = trim($_POST['lastname']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $role = trim($_POST['role']);

    // Debug log role value received
    error_log("Role value received from form: " . $role);
    
    // Validation basique
    if (empty($firstname) || empty($lastname) || empty($email) || empty($password) || empty($role)) {
        header("Location: ../sign.html?error=" . urlencode("Tous les champs sont obligatoires"));
        exit();
    }
    
    // Vérifier si l'email existe déjà
    $check_sql = "SELECT id FROM users WHERE email = ?";
    $check_stmt = $conn->prepare($check_sql);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        header("Location: ../sign.html?error=" . urlencode("Cet email est déjà utilisé"));
        exit();
    }
    
    // Mot de passe en clair
    $hashed_password = $password;

    // Forcer le rôle pour les inscriptions non-RH
    if ($role !== 'admin') {
        $role = 'employee';
    }

    // Insérer le nouvel utilisateur
    $insert_sql = "INSERT INTO users (firstname, lastname, email, password, role, is_active, created_at, updated_at) VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())";
    $insert_stmt = $conn->prepare($insert_sql);
    $insert_stmt->bind_param("sssss", $firstname, $lastname, $email, $hashed_password, $role);
    
    if ($insert_stmt->execute()) {
        // Récupérer l'ID du nouvel utilisateur
        $user_id = $insert_stmt->insert_id;
        
        // Connecter automatiquement l'utilisateur
        $_SESSION['user_id'] = $user_id;
        $_SESSION['firstname'] = $firstname;
        $_SESSION['lastname'] = $lastname;
        $_SESSION['email'] = $email;
        $_SESSION['role'] = $role;
        
        // Notifier les admins d'un nouvel employé (si non admin)
        if ($role !== 'admin') {
            $notif_title = 'Nouvel employé inscrit';
            $notif_msg = $firstname . ' ' . $lastname . ' vient de créer un compte.';
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
        }

        // Rediriger selon le rôle
        if ($role == 'admin') {
            header("Location: ../rh.php?success=Compte créé avec succès");
        } else {
            header("Location: ../employer.php?success=Compte créé avec succès");
        }
    } else {
        header("Location: ../sign.html?error=" . urlencode("Erreur lors de la création du compte"));
    }
    
    $check_stmt->close();
    $insert_stmt->close();
    $conn->close();
} else {
    header("Location: ../sign.html");
    exit();
}
?>