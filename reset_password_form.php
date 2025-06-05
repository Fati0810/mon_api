<?php
include 'config.php';

$token = $_GET['token'] ?? '';

if (!$token) {
    die("Lien invalide");
}

// Chercher l'utilisateur avec ce token
$stmt = $conn->prepare("SELECT id, reset_expires FROM users WHERE reset_token = ?");
$stmt->execute([$token]);
$user = $stmt->fetch();

if (!$user) {
    die("Lien invalide ou expiré");
}

// Vérifier que le token n'a pas expiré
if (new DateTime() > new DateTime($user['reset_expires'])) {
    die("Lien expiré");
}

$error = '';

// Traitement du formulaire POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $password = $_POST['password'] ?? '';
    $password_confirm = $_POST['password_confirm'] ?? '';

    if ($password !== $password_confirm) {
        $error = "Les mots de passe ne correspondent pas.";
    } elseif (strlen($password) < 6) {
        $error = "Le mot de passe doit contenir au moins 6 caractères.";
    } else {
        // Hasher le mot de passe
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        // Mettre à jour le mot de passe et supprimer le token pour éviter réutilisation
        $stmt = $conn->prepare("UPDATE users SET password = ?, reset_token = NULL, reset_expires = NULL WHERE id = ?");
        $stmt->execute([$passwordHash, $user['id']]);

        echo "Mot de passe réinitialisé avec succès. Vous pouvez maintenant vous connecter.";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8" />
    <title>Réinitialisation du mot de passe</title>
    <style>
        body {
            background: #f0f4f8;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            background: #fff;
            padding: 30px 65px 30px 40px;
            border-radius: 10px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.1);
            max-width: 400px;
            width: 100%;
        }
        h2 {
            margin-bottom: 25px;
            color: #050E7A;
            text-align: center;
        }
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: black;
        }
        input[type="password"] {
            width: 100%;
            padding: 12px 15px;
            margin-bottom: 20px;
            border: 1.5px solid #ccc;
            border-radius: 6px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        input[type="password"]:focus {
            border-color: #007BFF;
            outline: none;
            box-shadow: 0 0 6px rgba(0,123,255,0.3);
        }
        button {
            width: 100%;
            background-color: #050E7A;
            color: white;
            border: none;
            padding: 14px;
            border-radius: 6px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #0056b3;
        }
        p.error {
            background-color: #ffe6e6;
            color: #cc0000;
            border: 1px solid #cc0000;
            padding: 10px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Réinitialiser votre mot de passe</h2>

    <?php if (!empty($error)) echo "<p class='error'>$error</p>"; ?>

    <form method="post" novalidate>
        <label for="password">Nouveau mot de passe :</label>
        <input type="password" name="password" id="password" required minlength="6" placeholder="Entrez votre nouveau mot de passe" />

        <label for="password_confirm">Confirmer le mot de passe :</label>
        <input type="password" name="password_confirm" id="password_confirm" required minlength="6" placeholder="Confirmez votre mot de passe" />

        <button type="submit">Réinitialiser</button>
    </form>
</div>

</body>
</html>

