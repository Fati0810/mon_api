<?php
require_once __DIR__ . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$host = $_ENV['DB_HOST'] ?? 'localhost';
$dbname = $_ENV['DB_NAME'] ?? 'test_app';
$user = $_ENV['DB_USER'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? 'root';

define('JWT_SECRET_KEY', $_ENV['JWT_SECRET_KEY'] ?? 'MA_SUPER_CLE_SECRETE');
define('JWT_ISSUER', $_ENV['JWT_ISSUER'] ?? 'http://localhost');
define('JWT_AUDIENCE', $_ENV['JWT_AUDIENCE'] ?? 'http://localhost');
define('JWT_EXPIRATION_TIME', (int) ($_ENV['JWT_EXPIRATION_TIME'] ?? 3600));

try {
    $dsn = "mysql:host=$host;dbname=$dbname;charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $conn = new PDO($dsn, $user, $password, $options);

} catch (PDOException $e) {
    error_log("Erreur de connexion : " . $e->getMessage(), 3, __DIR__ . '/logs/error.log');

    if ($_ENV['APP_ENV'] === 'dev') {
        echo "Erreur : " . $e->getMessage();
    } else {
        echo "Erreur de connexion. Veuillez réessayer plus tard.";
    }
}
?>
