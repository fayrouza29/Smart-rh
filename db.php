<?php
// backend/db.php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "smart_rh";


$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("❌ Erreur de connexion à la base de données: " . $conn->connect_error);
}

// Définir l'encodage
$conn->set_charset("utf8mb4");
?>