<?php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

include '../includes/db.php'; // Veritabanı bağlantısı

function sendEmail($to, $subject, $message) {
    $mail = new PHPMailer(true);
    try {
        $mail->isSMTP();
        $mail->Host = 'smtp.gmail.com';
        $mail->SMTPAuth = true;
        $mail->Username = getenv('SMTP_USERNAME') ?: '';
        $mail->Password = getenv('SMTP_PASSWORD') ?: '';
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = 587;

        $mail->setFrom(getenv('SMTP_FROM') ?: $mail->Username, 'Lunera Evleri');
        $mail->addAddress($to);
        $mail->Subject = $subject;
        $mail->Body = $message;

        $mail->send();
        return ['status' => 'success', 'message' => 'E-posta gönderildi'];
    } catch (Exception $e) {
        return ['status' => 'error', 'message' => 'Hata: ' . $mail->ErrorInfo];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (isset($data['to']) && isset($data['subject']) && isset($data['message'])) {
        // Veritabanından e-posta çekme (örnek)
        $stmt = $pdo->prepare("SELECT email FROM users WHERE id = :id LIMIT 1");
        $stmt->execute(['id' => 1]); // Test için ID 1
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        $to = $user ? $user['email'] : $data['to']; // Veritabanı yoksa gelen adres
        $result = sendEmail($to, $data['subject'], $data['message']);
        echo json_encode($result);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Eksik veri']);
    }
}
?>