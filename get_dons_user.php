<?php
header('Content-Type: application/json');
require_once 'config.php'; // connexion PDO $conn

// Récupérer les données JSON envoyées
$data = json_decode(file_get_contents("php://input"), true);

$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['id_user'])) {
    http_response_code(400);
    echo json_encode(['error' => 'id_user manquant']);
    exit;
}

$id_user = $data['id_user'];

try {
    // Préparer la requête pour récupérer tous les dons de l'utilisateur
    $stmt = $conn->prepare("SELECT d.id_don, d.montant, d.contribution, d.total, d.date_don, t.numero_transaction, t.statut 
                            FROM dons d
                            LEFT JOIN transactions t ON d.id_don = t.id_don
                            WHERE d.id_user = ?
                            ORDER BY d.date_don DESC");
    $stmt->execute([$id_user]);
    $dons = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'dons' => $dons
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de la récupération des dons',
        'details' => $e->getMessage()
    ]);
}

?>