<?php
include 'config.php'; // Connexion PDO $conn

header('Content-Type: application/json; charset=utf-8');

// Récupération des données POST et nettoyage basique
$first_name = $_POST['first_name'] ?? '';
$last_name = $_POST['last_name'] ?? '';
$email = $_POST['email'] ?? '';
$email_confirm = $_POST['email_confirm'] ?? '';
$birthdate = $_POST['birthdate'] ?? '';
$address = $_POST['address'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$city = $_POST['city'] ?? '';
$country = $_POST['country'] ?? '';

// Vérification des paramètres requis
if ($first_name && $last_name && $email && $email_confirm && $birthdate && $address && $postal_code && $city && $country) {
    // Vérification que les emails correspondent
    if ($email !== $email_confirm) {
        echo json_encode(["status" => "error", "message" => "Les adresses e-mail ne correspondent pas."]);
        exit;
    }

    try {
        // Préparation de la requête SQL avec PDO
        $stmt = $conn->prepare("INSERT INTO users (first_name, last_name, email, birthdate, address, postal_code, city, country) VALUES (:first_name, :last_name, :email, :birthdate, :address, :postal_code, :city, :country)");

        // Exécution avec tableau associatif pour sécuriser les données
        $stmt->execute([
            ':first_name' => $first_name,
            ':last_name' => $last_name,
            ':email' => $email,
            ':birthdate' => $birthdate,
            ':address' => $address,
            ':postal_code' => $postal_code,
            ':city' => $city,
            ':country' => $country,
        ]);

        echo json_encode(["status" => "success", "message" => "Utilisateur ajouté"]);

    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Erreur lors de l'insertion : " . $e->getMessage()]);
    }

} else {
    echo json_encode(["status" => "error", "message" => "Paramètres manquants"]);
}
?>
