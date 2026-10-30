<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: http://localhost/site_management/login_test.html');
    exit;
}

$user_id = $_SESSION['user_id'];
$role = $_SESSION['role'] ?? 'user';
?>

<!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="UTF-8">
    <title>Dashboard</title>
    <style>
        body { font-family: Arial; }
        header { background: blue; color: white; padding: 10px; }
        nav { background: lightgray; padding: 5px; }
        section { margin: 10px; }
    </style>
</head>
<body>
    <header>
        <h1>Hoş Geldin, <?php echo $role; ?>!</h1>
    </header>
    <nav>
        <a href="#">Aidatlar</a> | <a href="#">Giderler</a> | <a href="#">Sorunlar</a>
    </nav>
    <section>
        <p>Kullanıcı ID: <?php echo $user_id; ?></p>
        <p>Bu dashboard sayfası, login sonrası görünecek. </p>
    </section>
    <footer>
        <p>Çıkış Yap: <a href="logout.php">Çıkış</a></p>
    </footer>
</body>
</html>