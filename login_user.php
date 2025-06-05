<?php
include 'config.php';

require 'vendor/autoload.php'; 
use Firebase\JWT\JWT;

header('Content-Type: application/json');

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur connexion BDD']);
    exit;
}

if (empty($_POST['email']) || empty($_POST['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Email et mot de passe sont requis.']);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

$stmt = $pdo->prepare("SELECT id, first_name, last_name, email, password, birthdate, address, postal_code, city, country, created_at FROM users WHERE email = :email");
$stmt->execute(['email' => $email]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Utilisateur non trouvé']);
    exit;
}

if (!password_verify($password, $user['password'])) {
    echo json_encode(['status' => 'error', 'message' => 'Mot de passe incorrect']);
    exit;
}

$payload = [
    "iss" => JWT_ISSUER,
    "aud" => JWT_AUDIENCE,
    "iat" => time(),
    "exp" => time() + JWT_EXPIRATION_TIME,
    "user_id" => $user['id'],
    "email" => $user['email'],
    "first_name" => $user['first_name']
];

$jwt = JWT::encode($payload, JWT_SECRET_KEY, 'HS256');

// Ne pas envoyer le mot de passe au client !
unset($user['password']);

echo json_encode([
    'status' => 'success',
    'message' => 'Connexion réussie.',
    'user' => $user,
    'token' => $jwt
]);

?>