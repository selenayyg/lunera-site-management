<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            $stmt = $pdo->prepare("INSERT INTO assets (name, barcode, location, responsible, purchase_date, cost) VALUES (:name, :barcode, :location, :responsible, :purchase_date, :cost)");
            $stmt->execute([
                ':name' => $data['name'],
                ':barcode' => $data['barcode'],
                ':location' => $data['location'],
                ':responsible' => $data['responsible'],
                ':purchase_date' => $data['purchase_date'],
                ':cost' => $data['cost']
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Demirbaş eklendi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz action']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action eksik']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM assets ORDER BY purchase_date DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>