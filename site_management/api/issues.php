<?php
header('Content-Type: application/json');
include '../includes/db.php';

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data || !isset($data['description']) || !isset($data['apartment_id'])) {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri!']);
        exit;
    }
    $stmt = $pdo->prepare("INSERT INTO issues (description, apartment_id, status, assigned_to, created_at) VALUES (:description, :apartment_id, 'open', :assigned_to, NOW())");
    try {
        $stmt->execute([
            'description' => $data['description'],
            'apartment_id' => $data['apartment_id'],
            'assigned_to' => $data['assigned_to'] ?? null
        ]);
        // E-posta bildirimi
        $to = 'admin@example.com'; // Gerçekte dinamik olmalı (assigned_to veya admin email)
        $subject = 'Yeni Sorun Kaydı';
        $message = "Yeni bir sorun kaydedildi:\nAçıklama: " . $data['description'] . "\nApartman ID: " . $data['apartment_id'];
        $ch = curl_init('http://localhost/site_management/api/send_email.php');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['to' => $to, 'subject' => $subject, 'message' => $message]));
        curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
        $response = curl_exec($ch);
        curl_close($ch);
        echo json_encode(['status' => 'success', 'message' => 'Sorun kaydedildi']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => 'Hata: ' . $e->getMessage()]);
    }
} elseif ($method == 'GET') {
    $stmt = $pdo->query("SELECT * FROM issues");
    echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
}
?>