<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $plate = $_POST['plate'];
    $apartment_id = $_POST['apartment_id'];
    $notes = $_POST['notes'];

    $stmt = $pdo->prepare("INSERT INTO visitors (name, plate, apartment_id, notes) VALUES (:name, :plate, :apartment_id, :notes)");
    $stmt->execute([
        ':name' => $name,
        ':plate' => $plate,
        ':apartment_id' => $apartment_id,
        ':notes' => $notes
    ]);
    header("Location: http://localhost/site_management/public/visitor_tracking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Güvenlik ve Misafir Kayıtları</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FA; }
        .card { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Güvenlik ve Misafir Kayıtları</h1>
        <div class="card">
            <h2>Yeni Misafir Kaydı</h2>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="name" class="form-label">İsim</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="plate" class="form-label">Plaka</label>
                    <input type="text" class="form-control" id="plate" name="plate">
                </div>
                <div class="mb-3">
                    <label for="apartment_id" class="form-label">Apartman ID</label>
                    <input type="number" class="form-control" id="apartment_id" name="apartment_id" required>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notlar</label>
                    <textarea class="form-control" id="notes" name="notes"></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kaydet</button>
            </form>
        </div>
        <div class="card mt-4">
            <h2>Misafir Listesi</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>İsim</th>
                        <th>Plaka</th>
                        <th>Apartman ID</th>
                        <th>Tarih</th>
                        <th>Notlar</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM visitors ORDER BY visit_date DESC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['plate']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['apartment_id']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['visit_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['notes']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>