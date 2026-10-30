<?php
session_start();
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../includes/db.php';
if ($_SERVER['REQUEST_METHOD'] !== 'POST') { http_response_code(405); echo json_encode(['success'=>false,'message'=>'Method not allowed']); exit; }
$data = json_decode(file_get_contents('php://input'), true) ?: [];
$email = trim($data['email'] ?? $data['username'] ?? '');
$password = $data['password'] ?? '';
$stmt = $pdo->prepare('SELECT id, name, email, password, role FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !password_verify($password, $user['password'])) { http_response_code(401); echo json_encode(['success'=>false,'message'=>'Invalid credentials']); exit; }
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
echo json_encode(['success'=>true,'message'=>'Login successful','user'=>['id'=>$user['id'],'name'=>$user['name']]]);
