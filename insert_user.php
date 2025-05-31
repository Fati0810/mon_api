<?php
include 'config.php';

header('Content-Type: application/json; charset=utf-8');

function respond(string $status, string $message, $data = null): void {
    $response = ["status" => $status, "message" => $message];
    if ($data !== null) {
        $response["data"] = $data;
    }
    echo json_encode($response);
    exit;
}

function clean_input(string $data): string {
    return htmlspecialchars(trim(strip_tags($data)), ENT_QUOTES, 'UTF-8');
}

function validate_email(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

function validate_name(string $name): bool {
    return preg_match("/^[\p{L} '-]{2,50}$/u", $name);
}

function validate_password(string $password): bool {
    return strlen($password) >= 6;
}

function validate_birthdate(string $date): bool {
    $dateObj = DateTime::createFromFormat('d/m/Y', $date);
    if (!$dateObj) return false;
    $now = new DateTime();
    return $dateObj <= $now;
}

function validate_address(string $address): bool {
    return preg_match("/^[\p{L}0-9\s,.'-]{5,100}$/u", $address);
}

function validate_postal_code(string $postal_code): bool {
    return preg_match("/^\d{5}$/", $postal_code);
}

function validate_city_country(string $text): bool {
    return preg_match("/^[\p{L}\s'-]{2,50}$/u", $text);
}

$first_name = clean_input($_POST['first_name'] ?? '');
$last_name = clean_input($_POST['last_name'] ?? '');
$email = clean_input($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';
$password_confirm = $_POST['password_confirm'] ?? '';
$birthdate = clean_input($_POST['birthdate'] ?? '');
$address = clean_input($_POST['address'] ?? '');
$postal_code = clean_input($_POST['postal_code'] ?? '');
$city = clean_input($_POST['city'] ?? '');
$country = clean_input($_POST['country'] ?? '');

$errors = [];

if (!$first_name) $errors['first_name'] = "Le prénom est obligatoire";
elseif (!validate_name($first_name)) $errors['first_name'] = "Prénom invalide";

if (!$last_name) $errors['last_name'] = "Le nom est obligatoire";
elseif (!validate_name($last_name)) $errors['last_name'] = "Nom invalide";

if (!$email) $errors['email'] = "L'email est obligatoire";
elseif (!validate_email($email)) $errors['email'] = "Email invalide";

if (!$password) $errors['password'] = "Le mot de passe est obligatoire";
elseif (!validate_password($password)) $errors['password'] = "Le mot de passe doit contenir au moins 6 caractères";

if (!$password_confirm) $errors['password_confirm'] = "La confirmation du mot de passe est obligatoire";
elseif ($password !== $password_confirm) $errors['password_confirm'] = "Les mots de passe ne correspondent pas";

if (!$birthdate) $errors['birthdate'] = "La date de naissance est obligatoire";
elseif (!validate_birthdate($birthdate)) $errors['birthdate'] = "Date de naissance invalide ou future";

if (!$address) $errors['address'] = "L'adresse est obligatoire";
elseif (!validate_address($address)) $errors['address'] = "Adresse invalide";

if (!$postal_code) $errors['postal_code'] = "Le code postal est obligatoire";
elseif (!validate_postal_code($postal_code)) $errors['postal_code'] = "Code postal invalide";

if (!$city) $errors['city'] = "La ville est obligatoire";
elseif (!validate_city_country($city)) $errors['city'] = "Ville invalide";

if (!$country) $errors['country'] = "Le pays est obligatoire";
elseif (!validate_city_country($country)) $errors['country'] = "Pays invalide";

if (!empty($errors)) {
    respond('error', 'Validation échouée', $errors);
}

$dateObj = DateTime::createFromFormat('d/m/Y', $birthdate);
$birthdate_sql = $dateObj->format('Y-m-d');

try {
    $checkStmt = $conn->prepare("SELECT COUNT(*) FROM users WHERE email = :email");
    $checkStmt->execute([':email' => $email]);
    if ($checkStmt->fetchColumn() > 0) {
        respond('error', "Cet email est déjà utilisé.");
    }
} catch (PDOException $e) {
    file_put_contents('error_pdo.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    respond('error', "Erreur serveur lors de la vérification d'email.");
}

$password_hash = password_hash($password, PASSWORD_DEFAULT);

try {
    $stmt = $conn->prepare("
        INSERT INTO users 
        (first_name, last_name, email, password, birthdate, address, postal_code, city, country) 
        VALUES 
        (:first_name, :last_name, :email, :password, :birthdate, :address, :postal_code, :city, :country)
    ");

    $stmt->execute([
        ':first_name' => $first_name,
        ':last_name' => $last_name,
        ':email' => $email,
        ':password' => $password_hash,
        ':birthdate' => $birthdate_sql,
        ':address' => $address,
        ':postal_code' => $postal_code,
        ':city' => $city,
        ':country' => $country,
    ]);

    respond('success', "Utilisateur ajouté");

} catch (PDOException $e) {
    file_put_contents('error_pdo.log', date('Y-m-d H:i:s') . " - " . $e->getMessage() . "\n", FILE_APPEND);
    respond('error', "Erreur serveur lors de l'insertion.");
}
?>
