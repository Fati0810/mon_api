<?php
include 'config.php'; // fichier avec la connexion PDO $conn

header('Content-Type: application/json; charset=utf-8');

try {
    $query = "SELECT first_name, last_name, email, birthdate, address, postal_code, city, country FROM users";
    $stmt = $conn->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        "status" => "success",
        "users" => $users
    ]);
} catch (PDOException $e) {
    echo json_encode([
        "status" => "error",
        "message" => $e->getMessage()
    ]);
}
?>
