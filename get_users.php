<?php
include 'config.php'; // fichier avec la connexion PDO $conn
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des utilisateurs</title>
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ccc;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f3f3f3;
        }
    </style>
</head>
<body>
    <h1>Utilisateurs inscrits</h1>
    <table id="users-table">
        <thead>
            <tr>
                <th>Prénom</th>
                <th>Nom</th>
                <th>Email</th>
                <th>Date de naissance</th>
                <th>Adresse</th>
                <th>Code postal</th>
                <th>Ville</th>
                <th>Pays</th>
            </tr>
        </thead>
        <tbody>
            <?php
            try {
                $query = "SELECT first_name, last_name, email, birthdate, address, postal_code, city, country FROM users";
                $stmt = $conn->query($query);
                $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($users as $user) {
                    echo "<tr>";
                    echo "<td>" . htmlspecialchars($user['first_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['last_name']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['email']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['birthdate']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['address']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['postal_code']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['city']) . "</td>";
                    echo "<td>" . htmlspecialchars($user['country']) . "</td>";
                    echo "</tr>";
                }
            } catch (PDOException $e) {
                echo "<tr><td colspan='8'>Erreur : " . htmlspecialchars($e->getMessage()) . "</td></tr>";
            }
            ?>
        </tbody>
    </table>
</body>
</html>
