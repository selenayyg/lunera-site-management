<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

$user_id = $_SESSION['user_id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $area = $_POST['area'];
    $start_time = $_POST['start_time'];
    $end_time = $_POST['end_time'];

    $stmt = $pdo->prepare("INSERT INTO bookings (area, user_id, start_time, end_time) VALUES (:area, :user_id, :start_time, :end_time)");
    $stmt->execute([
        ':area' => $area,
        ':user_id' => $user_id,
        ':start_time' => $start_time,
        ':end_time' => $end_time
    ]);
    header("Location: http://localhost/site_management/public/booking_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Ortak Alan Randevusu</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FA; }
        .card { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Ortak Alan Randevusu</h1>
        <div class="card">
            <h2>Yeni Randevu Oluştur</h2>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="area" class="form-label">Alan (örneğin Tenis Kortu)</label>
                    <input type="text" class="form-control" id="area" name="area" required>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <label for="start_time" class="form-label">Başlangıç Saati</label>
                        <input type="datetime-local" class="form-control" id="start_time" name="start_time" required>
                    </div>
                    <div class="col-md-6">
                        <label for="end_time" class="form-label">Bitiş Saati</label>
                        <input type="datetime-local" class="form-control" id="end_time" name="end_time" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100 mt-3">Kaydet</button>
            </form>
        </div>
        <div class="card mt-4">
            <h2>Randevu Listesi</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Alan</th>
                        <th>Kullanıcı ID</th>
                        <th>Başlangıç</th>
                        <th>Bitiş</th>
                        <th>Durum</th>
                        <th>İşlem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM bookings ORDER BY start_time ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['area']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['user_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['start_time']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['end_time']) . "</td>";
                        echo "<td>" . ($row['status'] == 'cancelled' ? 'İptal' : 'Rezerve') . "</td>";
                        if ($row['status'] == 'booked') {
                            echo "<td><a href='?cancel=" . $row['id'] . "' class='btn btn-warning btn-sm'>İptal</a></td>";
                        } else {
                            echo "<td>İptal Edildi</td>";
                        }
                        echo "</tr>";
                    }
                    if (isset($_GET['cancel'])) {
                        $stmt = $pdo->prepare("UPDATE bookings SET status = 'cancelled' WHERE id = :id");
                        $stmt->execute([':id' => $_GET['cancel']]);
                        header("Location: http://localhost/site_management/public/booking_management.php");
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>