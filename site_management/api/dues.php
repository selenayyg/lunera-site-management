<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            if (!isset($data['apartment_id']) || !isset($data['amount']) || !isset($data['month'])) {
                echo json_encode(['status' => 'error', 'message' => 'Eksik veri!']);
                exit;
            }
            $stmt = $pdo->prepare("INSERT INTO dues (apartment_id, amount, month, due_date, created_at, paid) VALUES (:apartment_id, :amount, :month, :due_date, NOW(), 0)");
            try {
                $stmt->execute([
                    'apartment_id' => $data['apartment_id'],
                    'amount' => $data['amount'],
                    'month' => $data['month'],
                    'due_date' => $data['due_date'] ?? date('Y-m-t')
                ]);
                echo json_encode(['status' => 'success', 'message' => 'Aidat kaydedildi']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        } elseif ($data['action'] == 'pay') {
            if (!isset($data['id'])) {
                echo json_encode(['status' => 'error', 'message' => 'Aidat ID eksik!']);
                exit;
            }
            $stmt = $pdo->prepare("UPDATE dues SET paid = 1, paid_at = NOW() WHERE id = :id");
            try {
                $stmt->execute(['id' => $data['id']]);
                echo json_encode(['status' => 'success', 'message' => 'Ödeme simüle edildi']);
            } catch (PDOException $e) {
                echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
            }
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action belirtilmedi!']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM dues");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>