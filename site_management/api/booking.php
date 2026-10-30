<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['action'])) {
        if ($data['action'] == 'create') {
            $stmt = $pdo->prepare("INSERT INTO bookings (area, user_id, start_time, end_time) VALUES (:area, :user_id, :start_time, :end_time)");
            $stmt->execute([
                ':area' => $data['area'],
                ':user_id' => $data['user_id'],
                ':start_time' => $data['start_time'],
                ':end_time' => $data['end_time']
            ]);
            echo json_encode(['status' => 'success', 'message' => 'Randevu kaydedildi']);
        } else if ($data['action'] == 'cancel') {
            $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
            $stmt->execute([':id' => $data['id']]);
            echo json_encode(['status' => 'success', 'message' => 'Randevu iptal edildi']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Geçersiz action']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Action eksik']);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY start_time ASC");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>