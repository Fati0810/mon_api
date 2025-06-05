<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'vendor/autoload.php'; // Autoload pour PHPMailer et Dotenv
include 'config.php';

// Charger les variables d'environnement
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Content-Type: application/json');

date_default_timezone_set('Europe/Paris');

$data = $_POST;
$email = filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL);

if (!$email) {
    echo json_encode(['status' => 'error', 'message' => 'Email invalide']);
    exit;
}

// Vérifie si l'email existe
$stmt = $conn->prepare("SELECT id, first_name FROM users WHERE email = ?");
$stmt->execute([$email]);
$user = $stmt->fetch();


if (!$user) {
    echo json_encode(['status' => 'error', 'message' => 'Aucun utilisateur trouvé avec cet email']);
    exit;
}

$prenom = $user['first_name'] ?? '';

// Génère un token sécurisé
$token = bin2hex(random_bytes(16));
$expires = date('Y-m-d H:i:s', strtotime('+5 minutes'));

// Enregistre le token dans la base de données
$stmt = $conn->prepare("UPDATE users SET reset_token = ?, reset_expires = ? WHERE email = ?");
$stmt->execute([$token, $expires, $email]);

// Lien de réinitialisation basé sur la variable d'environnement
$resetLink = $_ENV['APP_URL'] . "/reset_password_form.php?token=$token";

// Email avec PHPMailer
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth = true;
    $mail->Username = $_ENV['SMTP_USERNAME'];
    $mail->Password = $_ENV['SMTP_PASSWORD'];
    $mail->SMTPSecure = $_ENV['SMTP_ENCRYPTION'];
    $mail->Port = $_ENV['SMTP_PORT'];

    $mail->setFrom($_ENV['SMTP_USERNAME'], 'Support Cœur de France');
    $mail->addAddress($email);
    $mail->Subject = 'Réinitialisation de votre mot de passe';

    $htmlMessage = '
    <html>
    <head>
        <meta charset="UTF-8">
        <style>
            body { font-family: Arial, sans-serif; background-color: #f8f9fa; color: #333; }
            .container { padding: 20px; background-color: #fff; border-radius: 8px; }
            h1 { color: #050E7A; }
            a { color: #050E7A; text-decoration: none; }
            a:hover { text-decoration: underline; }
            p { font-size: 16px; }
            .signature {
            margin-top: 25px;
            font-size: 14px;
            color: #555;
            text-align: center;
            line-height: 1.5;
            }

        </style>
    </head>
    <body>
        <div class="container">
            <h1>Réinitialisation de votre mot de passe</h1>
            <p>Bonjour ' . htmlspecialchars($prenom) . ',</p>
            <p>Cliquez sur le lien ci-dessous pour réinitialiser votre mot de passe :</p>
            <p><a href="' . $resetLink . '">' . $resetLink . '</a></p>
            <p>Ce lien est valable 5 minutes.</p>
            <p>Si vous n\'avez pas demandé cette réinitialisation, vous pouvez ignorer ce message.</p>
            <p class="signature">Cordialement,<br>L\'équipe <strong>Cœur de France</strong></p>
        </div>
    </body>
    </html>';

    $mail->isHTML(true);
    $mail->Body = $htmlMessage;
    $mail->AltBody = "Bonjour,\nCliquez sur le lien suivant pour réinitialiser votre mot de passe : $resetLink\nCe lien est valable 5 minutes.\nSi vous n'avez pas demandé cette réinitialisation, ignorez ce message.";

    $mail->CharSet = 'UTF-8';
    $mail->send();

    echo json_encode(['status' => 'success', 'message' => 'Email de réinitialisation envoyé']);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => 'Erreur PHPMailer : ' . $mail->ErrorInfo]);
}
?>
