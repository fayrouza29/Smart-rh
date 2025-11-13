<?php
// backend/users.php - Admin user management API
session_start();
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/db.php';

function respond($data, $code = 200) {
    http_response_code($code);
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit();
}

// Security: require admin
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    respond([ 'error' => 'Non autorisé' ], 401);
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // List users with optional search (q) and optional alphabet starts-with (starts)
    $q = isset($_GET['q']) ? trim($_GET['q']) : '';
    $starts = isset($_GET['starts']) ? trim($_GET['starts']) : '';

    if ($starts !== '') {
        // Starts-with filter on firstname/lastname/email
        $prefix = $starts . '%';
        $sql = "SELECT id, firstname, lastname, email, role, is_active, created_at, updated_at FROM users
                WHERE role = 'employee' AND (firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)
                ORDER BY firstname ASC, lastname ASC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $prefix, $prefix, $prefix);
    } else if ($q !== '') {
        $like = '%' . $q . '%';
        $sql = "SELECT id, firstname, lastname, email, role, is_active, created_at, updated_at FROM users
                WHERE role = 'employee' AND (firstname LIKE ? OR lastname LIKE ? OR email LIKE ?)
                ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sss', $like, $like, $like);
    } else {
        $sql = "SELECT id, firstname, lastname, email, role, is_active, created_at, updated_at FROM users WHERE role = 'employee' ORDER BY created_at DESC";
        $stmt = $conn->prepare($sql);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    $users = [];
    while ($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
    $stmt->close();
    respond([ 'users' => $users ]);
}

if ($method === 'POST') {
    $action = isset($_POST['action']) ? $_POST['action'] : 'add';

    if ($action === 'add') {
        $firstname = trim($_POST['firstname'] ?? '');
        $lastname = trim($_POST['lastname'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');
        // Force role to employee
        $role = 'employee';

        if ($firstname === '' || $lastname === '' || $email === '' || $password === '' || $role === '') {
            respond([ 'error' => 'Champs manquants' ], 400);
        }

        // Ensure unique email
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param('s', $email);
        $check->execute();
        $exists = $check->get_result()->num_rows > 0;
        $check->close();
        if ($exists) {
            respond([ 'error' => 'Cet email est déjà utilisé' ], 409);
        }

        // Note: plain password to match existing logic; consider hashing later
        $insert = $conn->prepare("INSERT INTO users (firstname, lastname, email, password, role, is_active, created_at, updated_at)
                                  VALUES (?, ?, ?, ?, ?, 1, NOW(), NOW())");
        $insert->bind_param('sssss', $firstname, $lastname, $email, $password, $role);
        if ($insert->execute()) {
            $id = $insert->insert_id;
            $insert->close();
            respond([
                'success' => true,
                'user' => [
                    'id' => $id,
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $email,
                    'role' => $role,
                    'is_active' => 1
                ]
            ], 201);
        } else {
            $insert->close();
            respond([ 'error' => "Erreur lors de l'ajout" ], 500);
        }
    }

    if ($action === 'delete') {
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        if ($id <= 0) {
            respond([ 'error' => 'ID invalide' ], 400);
        }
        // Prevent self-deletion
        if ($id === intval($_SESSION['user_id'])) {
            respond([ 'error' => 'Impossible de supprimer votre propre compte' ], 400);
        }

        // Ensure user exists
        $check = $conn->prepare("SELECT id, role FROM users WHERE id = ?");
        $check->bind_param('i', $id);
        $check->execute();
        $res = $check->get_result();
        $row = $res->fetch_assoc();
        $check->close();
        if (!$row) {
            respond([ 'error' => "Utilisateur introuvable" ], 404);
        }
        if ($row['role'] !== 'employee') {
            respond([ 'error' => "Suppression limitée aux employés" ], 400);
        }

        $del = $conn->prepare("DELETE FROM users WHERE id = ?");
        $del->bind_param('i', $id);
        if ($del->execute()) {
            $del->close();
            respond([ 'success' => true ]);
        } else {
            $del->close();
            respond([ 'error' => 'Erreur lors de la suppression' ], 500);
        }
    }

    respond([ 'error' => 'Action inconnue' ], 400);
}

respond([ 'error' => 'Méthode non autorisée' ], 405);
?>


