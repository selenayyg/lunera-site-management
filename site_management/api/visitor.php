<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            $stmt = $pdo->prepare("INSERT INTO visitors (name, plate, apartment_id, notes) VALUES (:name, :plate, :apt_id, :notes)");
            $stmt->execute([
                ':name' => $data['name'],
                ':plate' => $data['plate'] ?? null,
                ':apt_id' => $data['apartment_id'],
                ':notes' => $data['notes'] ?? null
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Misafir kaydı eklendi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz action']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action eksik']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM visitors ORDER BY visit_date DESC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>