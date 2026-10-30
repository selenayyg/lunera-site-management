<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    // Gider ekleme
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['description']) || !isset($data['amount']) || !isset($data['expense_date'])) {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri!']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO expenses (description, amount, expense_date, user_id) VALUES (:desc, :amt, :exp_date, :user_id)");
    try {
        $stmt->execute([
            'desc' => $data['description'],
            'amt' => floatval($data['amount']),
            'exp_date' => $data['expense_date'],
            'user_id' => $data['user_id'] ?? null // İsteğe bağlı, boş bırakılabilir
        ]);
        echo json_encode(['status' => 'success', 'message' => 'Gider kaydedildi']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
    }
} elseif ($method == 'GET') {
    // Gider listesi
    $stmt = $pdo->query("SELECT * FROM expenses");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>