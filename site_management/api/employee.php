<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            $stmt = $pdo->prepare("INSERT INTO employees (name, role, shift, performance_score, assignment) VALUES (:name, :role, :shift, :performance_score, :assignment)");
            $stmt->execute([
                ':name' => $data['name'],
                ':role' => $data['role'],
                ':shift' => $data['shift'],
                ':performance_score' => $data['performance_score'],
                ':assignment' => $data['assignment']
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Çalışan eklendi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz action']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action eksik']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM employees");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>