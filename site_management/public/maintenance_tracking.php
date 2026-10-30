<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];

    $stmt = $pdo->prepare("INSERT INTO maintenance (description, due_date) VALUES (:description, :due_date)");
    $stmt->execute([
        ':description' => $description,
        ':due_date' => $due_date
    ]);
    header("Location: http://localhost/site_management/public/maintenance_tracking.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Bakım Takibi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FA; }
        .card { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Bakım Takibi</h1>
        <div class="card">
            <h2>Yeni Bakım Kaydı</h2>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="description" class="form-label">Açıklama (örneğin Asansör Bakımı)</label>
                    <input type="text" class="form-control" id="description" name="description" required>
                </div>
                <div class="mb-3">
                    <label for="due_date" class="form-label">Vade Tarihi</label>
                    <input type="date" class="form-control" id="due_date" name="due_date" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kaydet</button>
            </form>
        </div>
        <div class="card mt-4">
            <h2>Bakım Listesi</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Açıklama</th>
                        <th>Vade Tarihi</th>
                        <th>Durum</th>
                        <th>Tamamla</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM maintenance ORDER BY due_date ASC");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['description']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['due_date']) . "</td>";
                        echo "<td>" . ($row['status'] == 'completed' ? 'Tamamlandı' : 'Bekliyor') . "</td>";
                        if ($row['status'] == 'pending') {
                            echo "<td><a href='?complete=" . $row['id'] . "' class='btn btn-success btn-sm'>Tamamla</a></td>";
                        } else {
                            echo "<td>Tamamlandı</td>";
                        }
                        echo "</tr>";
                    }
                    if (isset($_GET['complete'])) {
                        $stmt = $pdo->prepare("UPDATE maintenance SET status = 'completed' WHERE id = :id");
                        $stmt->execute([':id' => $_GET['complete']]);
                        header("Location: http://localhost/site_management/public/maintenance_tracking.php");
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>