<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $barcode = $_POST['barcode'];
    $location = $_POST['location'];
    $responsible = $_POST['responsible'];
    $purchase_date = $_POST['purchase_date'];
    $cost = $_POST['cost'];

    $stmt = $pdo->prepare("INSERT INTO assets (name, barcode, location, responsible, purchase_date, cost) VALUES (:name, :barcode, :location, :responsible, :purchase_date, :cost)");
    $stmt->execute([
        ':name' => $name,
        ':barcode' => $barcode,
        ':location' => $location,
        ':responsible' => $responsible,
        ':purchase_date' => $purchase_date,
        ':cost' => $cost
    ]);
    header("Location: http://localhost/site_management/public/asset_tracking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Demirbaş Takibi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FA; }
        .card { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Demirbaş Takibi</h1>
        <div class="card">
            <h2>Yeni Demirbaş Ekle</h2>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="name" class="form-label">İsim</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="barcode" class="form-label">Barkod</label>
                    <input type="text" class="form-control" id="barcode" name="barcode" required>
                </div>
                <div class="mb-3">
                    <label for="location" class="form-label">Konum</label>
                    <input type="text" class="form-control" id="location" name="location" required>
                </div>
                <div class="mb-3">
                    <label for="responsible" class="form-label">Sorumlu</label>
                    <input type="text" class="form-control" id="responsible" name="responsible" required>
                </div>
                <div class="mb-3">
                    <label for="purchase_date" class="form-label">Satın Alma Tarihi</label>
                    <input type="date" class="form-control" id="purchase_date" name="purchase_date" required>
                </div>
                <div class="mb-3">
                    <label for="cost" class="form-label">Maliyet</label>
                    <input type="number" class="form-control" id="cost" name="cost" step="0.01" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kaydet</button>
            </form>
        </div>
        <div class="card mt-4">
            <h2>Demirbaş Listesi</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>İsim</th>
                        <th>Barkod</th>
                        <th>Konum</th>
                        <th>Sorumlu</th>
                        <th>Satın Alma Tarihi</th>
                        <th>Maliyet</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM assets ORDER BY purchase_date DESC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['barcode']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['location']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['responsible']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['purchase_date']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['cost']) . " TL</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>