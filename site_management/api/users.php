<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';
if (empty($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') { http_response_code(403); echo json_encode(['status'=>'error','message'=>'Forbidden']); exit; }
if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    echo json_encode($pdo->query('SELECT id, name, surname, email, role FROM users')->fetchAll()); exit;
}
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); exit; }
$data = json_decode(file_get_contents('php://input'), true) ?: [];
if (($data['action'] ?? '') !== 'register' || empty($data['name']) || !filter_var($data['email'] ?? '', FILTER_VALIDATE_EMAIL) || empty($data['password'])) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Invalid registration data']); exit; }
$stmt = $pdo->prepare('INSERT INTO users (name, surname, phone, email, password, role) VALUES (?, ?, ?, ?, ?, ?)');
try {
    $stmt->execute([$data['name'], $data['surname'] ?? null, $data['phone'] ?? null, $data['email'], password_hash($data['password'], PASSWORD_DEFAULT), 'user']);
    echo json_encode(['status'=>'success','message'=>'User created']);
} catch (PDOException $e) { http_response_code(400); echo json_encode(['status'=>'error','message'=>'Could not create user']); }
