<?php
header('Content-Type: application/json');
require_once 'config.php'; // inclut la connexion PDO $conn

// Lecture des données JSON reçues
$data = json_decode(file_get_contents("php://input"), true);

if (!isset($data['user_id'], $data['montant'], $data['contribution'], $data['total'])) {
    http_response_code(400);
    echo json_encode(['error' => 'Données manquantes.']);
    exit;
}

$id_user = $data['user_id'];
$montant = $data['montant'];
$contribution = $data['contribution'];
$total = $data['total'];
$date = $data['date']; 


try {
    $conn->beginTransaction();

    // Insertion don
    $stmt = $conn->prepare("INSERT INTO dons (id_user, montant, contribution, total, date_don) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$id_user, $montant, $contribution, $total, $date]);    
    $id_don = $conn->lastInsertId();

    // Génération numéro transaction simulée
    $numero_transaction = uniqid('txn_');

    // Insertion transaction
    $statut = 'payé'; // attention à l'encodage dans la BDD
    $stmt = $conn->prepare("INSERT INTO transactions (id_don, numero_transaction, statut) VALUES (?, ?, ?)");
    $stmt->execute([$id_don, $numero_transaction, $statut]);

    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Don enregistré avec succès.',
        
        'numero_transaction' => $numero_transaction,
        'id_don' => $id_don
    ]);
} catch (Exception $e) {
    $conn->rollBack();
    http_response_code(500);
    echo json_encode([
        'error' => 'Erreur lors de l’enregistrement du don.',
        'details' => $e->getMessage()
    ]);
}
?>