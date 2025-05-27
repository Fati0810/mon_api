<?php
// Chargement des variables d'environnement (ex: via .env ou config séparée)
$host = getenv('DB_HOST') ?: 'localhost';
$dbname = getenv('DB_NAME') ?: 'test_app';
$user = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: 'root';

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Mode Exception
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Fetch en tableau associatif
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Désactive les requêtes émulées
    ];
    
    $conn = new PDO($dsn, $user, $password, $options);

    // Connexion réussie (tu peux ici faire des logs si tu veux)
} catch (PDOException $e) {
    // En production : ne jamais afficher directement l'erreur
    error_log("Erreur de connexion à la base de données : " . $e->getMessage(), 3, __DIR__ . '/logs/error.log');
    
    // Message générique
    if ($_ENV['APP_ENV'] === 'dev') {
        echo "Une erreur est survenue : " . $e->getMessage();
    } else {
        echo "Une erreur de connexion est survenue. Veuillez réessayer plus tard.";
    }
}
?>
