<?php
session_start();
include '../includes/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['register'])) {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $phone = $_POST['phone'];
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Kayıt sırasında hash
        $role = 'user'; // Public registration cannot grant administrator access.

        $stmt = $pdo->prepare("INSERT INTO users (name, surname, phone, email, password, role) VALUES (:name, :surname, :phone, :email, :password, :role)");
        try {
            $stmt->execute([
                ':name' => $name,
                ':surname' => $surname,
                ':phone' => $phone,
                ':email' => $email,
                ':password' => $password,
                ':role' => $role
            ]);
            $success = "Kayıt başarılı! Lütfen giriş yapın.";
        } catch (PDOException $e) {
            $error = "Kayıt başarısız: " . $e->getMessage();
        }
    } elseif (isset($_POST['login'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute([':email' => $email]);

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) { // Hash ile doğrulama
                $_SESSION['user_id'] = $user['id'];
                header("Location: http://localhost/site_management/public/index.php");
                exit();
            } else {
                $error = "E-posta veya şifre hatalı.";
            }
        } else {
            $error = "E-posta veya şifre hatalı.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lunera Evleri - Giriş ve Kayıt</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital@1&family=Poppins:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            margin: 0;
            padding: 0;
            background: url('https://www.muglam.com.tr/wp-content/uploads/2024/05/gocek-portville.jpg') no-repeat center center/cover fixed; /* Kendi URL'n */
            height: 100vh;
            overflow: auto;
            color: #303030;
            position: relative;
        }
        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            position: relative;
            z-index: 1;
        }
        .container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.4); /* Hafif saydam overlay */
            z-index: -1;
        }
        .login-section {
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .login-box, .register-box {
            background-color: rgba(245, 245, 220, 0.9); /* Bej ile saydamlık */
            padding: 20px;
            border: 2px solid #228B22; /* Orman Yeşili */
            border-radius: 10px;
            margin: 10px;
            width: 300px;
            display: inline-block;
            vertical-align: top;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        }
        .login-box h2, .register-box h2 {
            color: #8B4513; /* Kahverengi */
            font-family: 'Playfair Display', serif;
            font-style: italic;
            margin-bottom: 15px;
        }
        .form-label {
            color: #8B4513; /* Kahverengi */
            font-weight: 500;
        }
        .form-control {
            border: 1px solid #8B4513; /* Kahverengi */
            border-radius: 5px;
        }
        .btn-primary {
            background-color: #228B22; /* Orman Yeşili */
            border: none;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #8B4513; /* Kahverengi */
        }
        .alert {
            margin-bottom: 20px;
            text-align: center;
            background-color: rgba(245, 245, 220, 0.9); /* Bej */
            border: 1px solid #8B4513; /* Kahverengi */
        }
        @media (max-width: 768px) {
            .login-box, .register-box {
                width: 100%;
                margin: 10px 0;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="login-section">
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>

            <!-- Kayıt Formu -->
            <div class="register-box">
                <h2>Kayıt Ol 🌿</h2>
                <form method="POST" class="mt-3">
                    <div class="mb-3">
                        <label for="name" class="form-label">Ad</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="surname" class="form-label">Soyad</label>
                        <input type="text" class="form-control" id="surname" name="surname" required>
                    </div>
                    <div class="mb-3">
                        <label for="phone" class="form-label">Telefon</label>
                        <input type="tel" class="form-control" id="phone" name="phone" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">E-posta</label>
                        <input type="email" class="form-control" id="email" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">Şifre</label>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                    <div class="mb-3">
                        <label for="role" class="form-label">Rol</label>
                        <select class="form-select" id="role" name="role" required>
                            <option value="admin">Yönetici</option>
                            <option value="user">Kullanıcı</option>
                        </select>
                    </div>
                    <button type="submit" name="register" class="btn btn-primary w-100">Kayıt Ol</button>
                </form>
            </div>

            <!-- Giriş Formu -->
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
            </div>
        </div>
    </div>
</body>
</html>