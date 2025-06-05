<?php
include 'config.php';

header('Content-Type: application/json');

$user_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT, ["options" => ["min_range" => 1]]);
if (!$user_id) {
    http_response_code(400);
    echo json_encode(['error' => 'user_id invalide'], JSON_PRETTY_PRINT);
    exit;
}

try {
    $stmt = $conn->prepare('SELECT first_name, last_name, email, birthdate, address, postal_code, city, country FROM users WHERE id = ?');
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if ($user) {
        http_response_code(200);
        echo json_encode($user, JSON_PRETTY_PRINT);
    } else {
        http_response_code(404);
        echo json_encode(['error' => 'Utilisateur non trouvé'], JSON_PRETTY_PRINT);
    }
} catch (Exception $e) {
    error_log("Erreur get_user.php : " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur'], JSON_PRETTY_PRINT);
}
exit;
?>
