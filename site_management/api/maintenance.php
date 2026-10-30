<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            $stmt = $pdo->prepare("INSERT INTO maintenance (description, due_date) VALUES (:description, :due_date)");
            $stmt->execute([
                ':description' => $data['description'],
                ':due_date' => $data['due_date']
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Bakım kaydı eklendi']);
        } else if ($data['action'] == 'complete') {
            $stmt = $pdo->prepare("UPDATE maintenance SET status = 'completed', last_maintenance = NOW() WHERE id = :id");
            $stmt->execute([':id' => $data['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Bakım tamamlandı']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz action']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action eksik']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM maintenance ORDER BY due_date ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>