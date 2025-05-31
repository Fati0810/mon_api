<?php
require 'vendor/autoload.php'; 
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header('Content-Type: application/json');

// Clé secrète (à garder confidentielle)
$secret_key = "MA_SUPER_CLE_SECRETE";

// Vérifie que les champs email et password ont été envoyés
if (empty($_POST['email']) || empty($_POST['password'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Email et mot de passe sont requis.'
    ]);
    exit;
}

$email = $_POST['email'];
$password = $_POST['password'];

// ⚠️ À remplacer par une vérification réelle en base de données
if ($email === 'bonjour@email.com' && $password === '123456') {

    // Données que tu veux stocker dans le token
    $payload = [
        "iss" => "http://192.168.1.100:8888",     // Émetteur
        "aud" => "http://192.168.1.100:8888",     // Destinataire
        "iat" => time(),                         // Date d’émission
        "exp" => time() + 3600,                  // Expiration dans 1 heure
        "email" => $email                        // Donnée personnalisée
    ];

    // Génère le token JWT
    $jwt = JWT::encode($payload, $secret_key, 'HS256');

    // Réponse JSON
    echo json_encode([
        'status' => 'success',
        'message' => 'Connexion réussie.',
        'email' => $email,
        'token' => $jwt
    ]);

} else {
    echo json_encode([
        'status' => 'error',
        'message' => 'Identifiants invalides.'
    ]);
}
?>