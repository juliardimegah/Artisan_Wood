<?php
session_start();
include '../db_connect.php';

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    // Anda harus menggunakan password_verify() dengan hash yang disimpan di DB.
    // Untuk contoh ini, kita tetap gunakan SHA256, tapi ini tidak direkomendasikan untuk produksi.
    $password = hash('sha256', $_POST['password']);

    $stmt = $conn->prepare("SELECT * FROM admin WHERE username=? AND password=?");
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        header("Location: index.php");
        exit;
    } else {
        $error = "Username atau password salah!";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - Artisan Wood CMS</title>
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="login-body">

<div class="login-container">
    <div class="login-box">
        <h2><i class="fas fa-user-shield"></i> Admin Login</h2>
        <p>Welcome to the Artisan Wood CMS</p>
        
        <?php if (!empty($error)): ?>
            <div class="login-error"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST" action="login-admin.php">
            <div class="input-group">
                <i class="fas fa-user"></i>
                <input type="text" name="username" placeholder="Username" required>
            </div>
            <div class="input-group">
                <i class="fas fa-lock"></i>
                <input type="password" name="password" placeholder="Password" required>
            </div>
            <button type="submit">Login</button>
        </form>
    </div>
</div>

</body>
</html>
