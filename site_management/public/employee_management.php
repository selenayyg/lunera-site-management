<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $role = $_POST['role'];
    $shift = $_POST['shift'];
    $performance_score = $_POST['performance_score'];
    $assignment = $_POST['assignment'];

    $stmt = $pdo->prepare("INSERT INTO employees (name, role, shift, performance_score, assignment) VALUES (:name, :role, :shift, :performance_score, :assignment)");
    $stmt->execute([
        ':name' => $name,
        ':role' => $role,
        ':shift' => $shift,
        ':performance_score' => $performance_score,
        ':assignment' => $assignment
    ]);
    header("Location: http://localhost/site_management/public/employee_management.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Çalışan Takibi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Poppins', sans-serif; background-color: #F8F9FA; }
        .card { max-width: 800px; margin: 20px auto; padding: 20px; background-color: #FFFFFF; border-radius: 10px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center text-primary mb-4">Çalışan Takibi</h1>
        <div class="card">
            <h2>Yeni Çalışan Ekle</h2>
            <form method="POST" class="mt-3">
                <div class="mb-3">
                    <label for="name" class="form-label">Ad</label>
                    <input type="text" class="form-control" id="name" name="name" required>
                </div>
                <div class="mb-3">
                    <label for="role" class="form-label">Rol</label>
                    <select class="form-select" id="role" name="role" required>
                        <option value="security">Güvenlik</option>
                        <option value="caretaker">Kapıcı</option>
                        <option value="technician">Teknik Personel</option>
                        <option value="gardener">Bahçıvan</option>
                        <option value="cleaner">Temizlik Personeli</option>
                        <option value="gym_attendant">Spor Salonu Görevlisi</option>
                        <option value="cafeteria_worker">Kafeterya Çalışanı</option>
                        <option value="pool_attendant">Havuz Görevlisi</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="shift" class="form-label">Vardiya</label>
                    <input type="text" class="form-control" id="shift" name="shift" required>
                </div>
                <div class="mb-3">
                    <label for="performance_score" class="form-label">Performans Puanı</label>
                    <input type="number" class="form-control" id="performance_score" name="performance_score" min="0" max="100" required>
                </div>
                <div class="mb-3">
                    <label for="assignment" class="form-label">Görev Ataması</label>
                    <textarea class="form-control" id="assignment" name="assignment" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary w-100">Kaydet</button>
            </form>
        </div>
        <div class="card mt-4">
            <h2>Çalışan Listesi</h2>
            <table class="table table-striped mt-3">
                <thead>
                    <tr>
                        <th>Ad</th>
                        <th>Rol</th>
                        <th>Vardiya</th>
                        <th>Performans Puanı</th>
                        <th>Görev Ataması</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $stmt = $pdo->query("SELECT * FROM employees");
                    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row['name']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['role']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['shift']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['performance_score']) . "</td>";
                        echo "<td>" . htmlspecialchars($row['assignment']) . "</td>";
                        echo "</tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>