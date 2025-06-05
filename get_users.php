<?php
include 'config.php'; 

header('Content-Type: application/json');

try {
    $query = "SELECT first_name, last_name, email, birthdate, password, address, postal_code, city, country FROM users";
    $stmt = $conn->query($query);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($users as &$user) {
        $user['first_name'] = ucfirst(strtolower($user['first_name']));
        $user['last_name'] = ucfirst(strtolower($user['last_name']));
        $user['city'] = ucfirst(strtolower($user['city']));
        $user['country'] = ucfirst(strtolower($user['country']));
        $user['birthdate'] = date('d-m-Y', strtotime($user['birthdate']));
    }

    echo json_encode($users, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur : ' . $e->getMessage()], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
}

?>