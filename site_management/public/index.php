<?php
session_start();
include '../includes/db.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: http://localhost/site_management/public/users.php");
    exit();
}

if (isset($_SESSION['user_id'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :id");
    $stmt->execute(['id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunera Evleri Yönetim</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(135deg, #2F2F2F 0%, #1A1A1A 100%); /* Dark background */
            color: #D2B48C; /* Beige text */
            overflow-x: hidden;
            line-height: 1.6;
        }
        /* Giriş ve Kayıt Stilleri */
        .login-section {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        .login-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Hafif saydam overlay */
            z-index: -1;
        }
        .login-box {
            background-color: rgba(245, 245, 220, 0.9); /* Beige with transparency */
            padding: 20px;
            border: 2px solid #228B22; /* Forest Green */
            border-radius: 10px;
            margin: 10px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
            text-align: center;
        }
        .login-box h2 {
            color: #8B4513; /* Brown */
            font-family: 'Playfair Display', serif;
            font-style: italic;
            margin-bottom: 15px;
        }
        .form-label {
            color: #8B4513; /* Brown */
            font-weight: 500;
        }
        .form-control {
            border: 1px solid #8B4513; /* Brown */
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #228B22; /* Forest Green */
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #8B4513; /* Brown */
        }
        .alert {
            margin-bottom: 20px;
            text-align: center;
            background-color: rgba(245, 245, 220, 0.9); /* Beige */
            border: 1px solid #8B4513; /* Brown */
        }
        .register-link {
            margin-top: 15px;
            color: #228B22; /* Forest Green */
            text-decoration: none;
        }
        .register-link:hover {
            text-decoration: underline;
            color: #2E8B57; /* Lighter Forest Green */
        }
        /* Ana Sayfa Stilleri */
        .dashboard {
            display: none;
            padding-top: 90px;
        }
        .dashboard.active {
            display: block;
        }
        .banner {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            background: linear-gradient(90deg, #4A3728 0%, #8B6F47 100%); /* Brown gradient */
            color: #D2B48C; /* Beige */
            padding: 15px 30px;
            text-align: center;
            font-weight: 600;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            z-index: 900;
        }
        .container {
            max-width: 1300px;
            margin: 0 auto;
            padding: 30px 20px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-top: 30px;
        }
        .card {
            background: #F5F5DC; /* Beige for light card interior */
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.05);
            text-align: center;
            cursor: pointer;
            border-left: 6px solid #228B22; /* Forest Green */
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 6px 25px rgba(0, 0, 0, 0.1);
        }
        .card.fullscreen {
            position: fixed;
            top: 90px;
            left: 0;
            width: 100%;
            height: calc(100% - 90px);
            z-index: 999;
            background: #F5F5DC; /* Beige for light card interior */
            padding: 50px;
            overflow-y: auto;
            border-radius: 0;
        }
        .card h2 {
            font-weight: 600;
            color: #4A3728; /* Brown */
            margin-bottom: 15px;
            font-size: 1.5rem;
        }
        .card-content {
            height: 400px;
            overflow-y: auto;
        }
        .card-dues { border-left: 6px solid #228B22; }
        .card-expenses { border-left: 6px solid #8B6F47; }
        .card-issues { border-left: 6px solid #D2B48C; }
        .card-reports { border-left: 6px solid #228B22; }
        .card-email { border-left: 6px solid #8B6F47; }
        .card-employee { border-left: 6px solid #D2B48C; }
        .card-maintenance { border-left: 6px solid #8B6F47; }
        .card-visitor { border-left: 6px solid #D2B48C; }
        .card-asset { border-left: 6px solid #228B22; }
        .card-booking { border-left: 6px solid #8B6F47; }
        .close-btn {
            position: absolute;
            top: 15px;
            right: 15px;
            font-size: 1.8rem;
            color: #A52A2A;
            cursor: pointer;
            z-index: 1001;
            transition: color 0.3s ease;
        }
        .close-btn:hover {
            color: #8B0000;
        }
        .logout {
            text-align: center;
            margin-top: 30px;
        }
        .logout a {
            color: #D2B48C;
            background: linear-gradient(90deg, #A52A2A 0%, #8B0000 100%);
            padding: 12px 25px;
            border-radius: 8px;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        .logout a:hover {
            background: linear-gradient(90deg, #8B0000 0%, #6B1E1E 100%);
            transform: translateY(-2px);
        }
        @media (max-width: 768px) {
            .card-container { grid-template-columns: 1fr; }
            .card-content { height: auto; }
            .card.fullscreen { top: 70px; height: calc(100% - 70px); }
            .login-box { width: 100%; margin: 10px 0; }
            .banner { padding: 10px 20px; }
        }
    </style>
</head>
<body>
    <?php if (!isset($_SESSION['user_id'])): ?>
        <div class="login-section">
            <?php
            $error = '';
            if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
                $email = $_POST['email'];
                $password = $_POST['password'];
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
                $stmt->execute([':email' => $email]);
                if ($stmt->rowCount() > 0) {
                    $user = $stmt->fetch(PDO::FETCH_ASSOC);
                    if (password_verify($password, $user['password'])) {
                        $_SESSION['user_id'] = $user['id'];
                        echo "<script>window.location.href='http://localhost/site_management/public/index.php';</script>";
                        exit();
                    } else {
                        $error = "E-posta veya şifre hatalı.";
                    }
                } else {
                    $error = "E-posta veya şifre hatalı.";
                }
            }
            ?>
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <div class="login-box">
                <h2>Giriş Yap 🏠</h2>
                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="login-email" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="login-email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="login-password" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="login-password" name="password" required>
                    </div>
                    <button type="submit" name="login" class="btn btn-primary w-100">Giriş Yap</button>
                </form>
                <div class="register-link">
                    <a href="http://localhost/site_management/public/register.php">Kayıt Ol</a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="banner">Lunera Evleri Yönetim Paneli</div>
        <div class="dashboard active">
            <div class="container">
                <h1 class="text-3xl font-semibold mb-6" style="color: #D2B48C;">Hoş Geldiniz, <?php echo $user['name'] ?? 'Kullanıcı'; ?></h1>
                <div class="card-container">
                    <div class="card card-dues">
                        <h2>Aidat Yönetimi 🎯</h2>
                        <div class="card-content">
                            <form id="duesForm" class="p-4 border rounded-lg shadow-sm" style="background: #F5F5DC;">
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <label for="dues-apt-id" class="form-label">Daire ID</label>
                                        <input type="number" class="form-control" id="dues-apt-id" name="apartment_id" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dues-amount" class="form-label">Tutar (TL)</label>
                                        <input type="number" class="form-control" id="dues-amount" name="amount" step="0.01" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="dues-month" class="form-label">Ay</label>
                                        <input type="month" class="form-control" id="dues-month" name="month" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary mt-4 w-100">Kaydet</button>
                                    </div>
                                </div>
                            </form>
                            <p id="duesResult" class="mt-3 fw-bold" style="color: #228B22;"></p>
                            <table class="table table-striped mt-4">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Daire ID</th>
                                        <th>Tutar</th>
                                        <th>Ay</th>
                                        <th>Durum</th>
                                        <th>İşlem</th>
                                    </tr>
                                </thead>
                                <tbody id="duesListBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card card-expenses">
                        <h2>Gider Kaydı 🌱</h2>
                        <div class="card-content">
                            <form id="expensesForm" class="p-4 border rounded-lg shadow-sm" style="background: #F5F5DC;">
                                <div class="mb-3">
                                    <label for="description" class="form-label">Açıklama</label>
                                    <input type="text" class="form-control" id="description" required>
                                </div>
                                <div class="mb-3">
                                    <label for="amount" class="form-label">Tutar (TL)</label>
                                    <input type="number" class="form-control" id="amount" step="0.01" required>
                                </div>
                                <div class="mb-3">
                                    <label for="expense_date" class="form-label">Tarih</label>
                                    <input type="date" class="form-control" id="expense_date" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Kaydet</button>
                            </form>
                            <p id="result" class="mt-3 fw-bold" style="color: #228B22;"></p>
                            <canvas id="expensesChart" class="mt-5 w-100" style="max-height: 400px;"></canvas>
                        </div>
                    </div>
                    <div class="card card-issues">
                        <h2>Sorun Kaydı ⚠️</h2>
                        <div class="card-content">
                            <form id="issuesForm" class="p-4 border rounded-lg shadow-sm" style="background: #F5F5DC;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="issue-description" class="form-label">Açıklama</label>
                                        <input type="text" class="form-control" id="issue-description" name="description" required>
                                    </div>
                                    <div class="col-md-3">
                                        <label for="issue-apt-id" class="form-label">Daire ID</label>
                                        <input type="number" class="form-control" id="issue-apt-id" name="apartment_id" required>
                                    </div>
                                    <div class="col-md-3">
                                        <button type="submit" class="btn btn-primary mt-4 w-100">Kaydet</button>
                                    </div>
                                </div>
                            </form>
                            <p id="issuesResult" class="mt-3 fw-bold" style="color: #228B22;"></p>
                            <table class="table table-striped mt-4">
                                <thead>
                                    <tr>
                                        <th>ID</th>
                                        <th>Açıklama</th>
                                        <th>Daire ID</th>
                                        <th>Durum</th>
                                    </tr>
                                </thead>
                                <tbody id="issuesListBody"></tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card card-reports">
                        <h2>Raporlama 📊</h2>
                        <div class="card-content">
                            <p class="text-lg">Toplam Ödenen Aidat: <span id="totalPaid" class="font-semibold" style="color: #4A3728;"></span> TL</p>
                            <p class="text-lg">Açık Sorun Sayısı: <span id="openIssues" class="font-semibold" style="color: #4A3728;"></span></p>
                        </div>
                    </div>
                    <div class="card card-email">
                        <h2>E-posta Testi 📧</h2>
                        <div class="card-content">
                            <form id="emailForm" class="p-4 border rounded-lg shadow-sm" style="background: #F5F5DC;">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label for="email-to" class="form-label">Alıcı E-posta</label>
                                        <input type="email" class="form-control" id="email-to" name="to" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label for="email-subject" class="form-label">Konu</label>
                                        <input type="text" class="form-control" id="email-subject" name="subject" required>
                                    </div>
                                    <div class="col-md-12">
                                        <label for="email-message" class="form-label">Mesaj</label>
                                        <textarea class="form-control" id="email-message" name="message" rows="4" required></textarea>
                                    </div>
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-primary w-100">Gönder</button>
                                    </div>
                                </div>
                            </form>
                            <p id="emailResult" class="mt-3 fw-bold" style="color: #228B22;"></p>
                        </div>
                    </div>
                    <div class="card card-employee">
                        <h2>Çalışan Takibi 👥</h2>
                        <div class="card-content">
                            <iframe src="http://localhost/site_management/public/employee_management.php" style="width: 100%; height: 400px; border: none;"></iframe>
                        </div>
                    </div>
                    <div class="card card-maintenance">
                        <h2>Bakım Takibi 🛠️</h2>
                        <div class="card-content">
                            <iframe src="http://localhost/site_management/public/maintenance_tracking.php" style="width: 100%; height: 400px; border: none;"></iframe>
                        </div>
                    </div>
                    <div class="card card-visitor">
                        <h2>Güvenlik ve Misafir Kayıtları 🔐</h2>
                        <div class="card-content">
                            <iframe src="http://localhost/site_management/public/visitor_tracking.php" style="width: 100%; height: 400px; border: none;"></iframe>
                        </div>
                    </div>
                    <div class="card card-asset">
                        <h2>Demirbaş Takibi 📦</h2>
                        <div class="card-content">
                            <iframe src="http://localhost/site_management/public/asset_tracking.php" style="width: 100%; height: 400px; border: none;"></iframe>
                        </div>
                    </div>
                    <div class="card card-booking">
                        <h2>Ortak Alan Randevusu 📅</h2>
                        <div class="card-content">
                            <iframe src="http://localhost/site_management/public/booking_management.php" style="width: 100%; height: 400px; border: none;"></iframe>
                        </div>
                    </div>
                </div>
                <div class="logout">
                    <a href="http://localhost/site_management/logout.php">Çıkış Yap</a>
                </div>
            </div>
        </div>
    <?php endif; ?>

    <script>
        // Kartları tam ekran yap ve kapatmayı düzelt
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function(e) {
                if (!this.classList.contains('fullscreen')) {
                    this.classList.add('fullscreen');
                    const closeBtn = document.createElement('span');
                    closeBtn.classList.add('close-btn');
                    closeBtn.innerHTML = '&times;';
                    closeBtn.addEventListener('click', (e) => {
                        e.stopPropagation();
                        this.classList.remove('fullscreen');
                        if (this.contains(closeBtn)) {
                            this.removeChild(closeBtn);
                        }
                    });
                    this.appendChild(closeBtn);
                }
            });
        });

        // Aidat Yönetimi
        document.getElementById('duesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                action: 'create',
                apartment_id: document.getElementById('dues-apt-id').value,
                amount: document.getElementById('dues-amount').value,
                month: document.getElementById('dues-month').value
            };
            try {
                const response = await fetch('http://localhost/site_management/api/dues.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                document.getElementById('duesResult').innerText = result.message;
                if (result.status === 'success') {
                    loadDues();
                    showNotification("Yeni aidat kaydı eklendi!");
                }
            } catch (error) {
                document.getElementById('duesResult').innerText = 'Hata: ' + error.message;
            }
        });

        // Gider Kaydı
        let expensesChart = null;
        document.getElementById('expensesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const data = {
                description: document.getElementById('description').value,
                amount: document.getElementById('amount').value,
                expense_date: document.getElementById('expense_date').value,
                user_id: null
            };
            try {
                const response = await fetch('http://localhost/site_management/api/expenses.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(data)
                });
                const result = await response.json();
                document.getElementById('result').innerText = result.message;
                if (result.status === 'success') fetchChartData();
            } catch (error) {
                document.getElementById('result').innerText = 'Hata: ' + error.message;
            }
        });

        async function fetchChartData() {
            const response = await fetch('http://localhost/site_management/api/expenses.php');
            const data = await response.json();
            if (expensesChart) {
                expensesChart.destroy();
            }
            const groupedData = {};
            data.forEach(item => {
                if (!groupedData[item.description]) {
                    groupedData[item.description] = { amounts: [], dates: [] };
                }
                groupedData[item.description].amounts.push(item.amount);
                groupedData[item.description].dates.push(item.expense_date);
            });

            const labels = data.map(d => d.expense_date || 'Bilinmeyen');
            const datasets = Object.keys(groupedData).map((desc, index) => ({
                label: desc,
                data: groupedData[desc].amounts,
                backgroundColor: getColor(index),
                borderColor: '#D2B48C',
                borderWidth: 1
            }));

            expensesChart = new Chart(document.getElementById('expensesChart'), {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: datasets
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true, title: { display: true, text: 'Tutar (TL)' } } },
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: {
                            callbacks: {
                                label: function(context) {
                                    return `${context.dataset.label}: ${context.raw} TL`;
                                }
                            }
                        }
                    }
                }
            });
        }

        function getColor(index) {
            const colors = ['#228B22', '#8B6F47', '#D2B48C', '#4A3728', '#A52A2A', '#2E8B57'];
            return colors[index % colors.length];
        }

        fetchChartData();

        // Sorun Kaydı
        document.getElementById('issuesForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                description: document.getElementById('issue-description').value,
                apartment_id: document.getElementById('issue-apt-id').value
            };
            try {
                const response = await fetch('http://localhost/site_management/api/issues.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                document.getElementById('issuesResult').innerText = result.message;
                if (result.status === 'success') loadIssues();
            } catch (error) {
                document.getElementById('issuesResult').innerText = 'Hata: ' + error.message;
            }
        });

        // Raporlama
        async function loadReports() {
            try {
                const duesResponse = await fetch('http://localhost/site_management/api/dues.php');
                const dues = await duesResponse.json();
                const totalPaid = dues.filter(d => d.paid).reduce((sum, d) => sum + parseFloat(d.amount), 0);
                document.getElementById('totalPaid').textContent = totalPaid.toFixed(2);

                const issuesResponse = await fetch('http://localhost/site_management/api/issues.php');
                const issues = await issuesResponse.json();
                const openIssues = issues.filter(i => i.status === 'open').length;
                document.getElementById('openIssues').textContent = openIssues;
            } catch (error) {
                console.error('Raporlama hatası:', error);
            }
        }

        // E-posta Testi
        document.getElementById('emailForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = {
                to: document.getElementById('email-to').value,
                subject: document.getElementById('email-subject').value,
                message: document.getElementById('email-message').value
            };
            try {
                const response = await fetch('http://localhost/site_management/api/send_email.php', {
                    method: 'POST',
                    headers: {'Content-Type': 'application/json'},
                    body: JSON.stringify(formData)
                });
                const result = await response.json();
                document.getElementById('emailResult').innerText = result.message;
                if (result.status === 'error') {
                    document.getElementById('emailResult').style.color = '#A52A2A';
                } else {
                    document.getElementById('emailResult').style.color = '#228B22';
                }
            } catch (error) {
                document.getElementById('emailResult').innerText = 'Hata: ' + error.message;
                document.getElementById('emailResult').style.color = '#A52A2A';
            }
        });

        // Veri Yükleme Fonksiyonları
        async function loadDues() {
            try {
                const response = await fetch('http://localhost/site_management/api/dues.php');
                const dues = await response.json();
                const tbody = document.getElementById('duesListBody');
                tbody.innerHTML = '';
                dues.forEach(dues => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${dues.id}</td>
                        <td>${dues.apartment_id}</td>
                        <td>${dues.amount} TL</td>
                        <td>${dues.month}</td>
                        <td>${dues.paid ? 'Ödendi' : 'Ödenmedi'}</td>
                        <td><button class="btn btn-success btn-sm pay-btn" style="background: #228B22;" data-id="${dues.id}">Öde</button></td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Dues yükleme hatası:', error);
            }
        }

        async function loadIssues() {
            try {
                const response = await fetch('http://localhost/site_management/api/issues.php');
                const issues = await response.json();
                const tbody = document.getElementById('issuesListBody');
                tbody.innerHTML = '';
                issues.forEach(issue => {
                    const row = document.createElement('tr');
                    row.innerHTML = `
                        <td>${issue.id}</td>
                        <td>${issue.description}</td>
                        <td>${issue.apartment_id}</td>
                        <td>${issue.status}</td>
                    `;
                    tbody.appendChild(row);
                });
            } catch (error) {
                console.error('Issues yükleme hatası:', error);
            }
        }

        // Ödeme Simülasyonu
        document.addEventListener('click', async (e) => {
            if (e.target.classList.contains('pay-btn')) {
                const id = e.target.getAttribute('data-id');
                const data = { action: 'pay', id: id };
                try {
                    const response = await fetch('http://localhost/site_management/api/dues.php', {
                        method: 'POST',
                        headers: {'Content-Type': 'application/json'},
                        body: JSON.stringify(data)
                    });
                    const result = await response.json();
                    alert(result.message);
                    if (result.status === 'success') loadDues();
                } catch (error) {
                    alert('Hata: ' + error.message);
                }
            }
        });

        // Bildirim Fonksiyonu
        function showNotification(message) {
            if (!("Notification" in window)) return;
            if (Notification.permission === "granted") {
                new Notification("Yeni Bildirim", { body: message });
            } else if (Notification.permission !== "denied") {
                Notification.requestPermission().then(permission => {
                    if (permission === "granted") {
                        new Notification("Yeni Bildirim", { body: message });
                    }
                });
            }
        }

        // Sayfa yüklendiğinde verileri yükle (giriş yapılmışsa)
        window.onload = () => {
            if (document.querySelector('.dashboard.active')) {
                loadDues();
                fetchChartData();
                loadIssues();
                loadReports();
            }
        };
    </script>
</body>
</html>