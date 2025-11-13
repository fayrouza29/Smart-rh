<?php
// Au début du fichier
error_reporting(E_ALL);
ini_set('display_errors', 1);

// backend/log.php
session_start();
include "db.php";

// Après la connexion à la base de données
if (!$conn) {
    die("Erreur de connexion DB: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Préparer et exécuter la requête
    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        // Vérifier le mot de passe
        if ($password === $user['password']) {
            // Vérifier que le rôle sélectionné correspond au rôle de l'utilisateur
            if (!isset($_POST['role']) || $_POST['role'] !== $user['role']) {
                header("Location: ../log.html?error=" . urlencode("Rôle incorrect pour cet utilisateur"));
                exit();
            }

            // Démarrer la session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['firstname'] = $user['firstname'];
            $_SESSION['lastname'] = $user['lastname'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['role'] = $user['role'];

            // Rediriger selon le rôle
            if ($user['role'] === 'admin') {
                header("Location: ../rh.php");
            } else if ($user['role'] === 'employee') {
                header("Location: ../employer.php");
            } else {
                // Rôle inconnu, rediriger vers la page de connexion avec erreur
                header("Location: ../log.html?error=" . urlencode("Rôle utilisateur inconnu"));
            }
            exit();
        } else {
            header("Location: ../log.html?error=" . urlencode("Mot de passe incorrect"));
            exit();
        }
    } else {
        header("Location: ../log.html?error=" . urlencode("Utilisateur non trouvé"));
        exit();
    }

    $stmt->close();
} else {
    header("Location: ../log.html");
    exit();
}

$conn->close();
?>
