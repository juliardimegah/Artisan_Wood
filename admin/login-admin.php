<?php
session_start();
include '../db_connect.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Ambil data admin dari database
    $stmt = $conn->prepare("SELECT id, password FROM admin WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $admin = $result->fetch_assoc();

    if ($admin) {
        $stored_hash = $admin['password'];
        $is_legacy_hash = (strlen($stored_hash) == 64 && ctype_xdigit($stored_hash));

        // Verifikasi password
        if (password_verify($password, $stored_hash) || ($is_legacy_hash && hash_equals($stored_hash, hash('sha256', $password)))) {
            
            // Jika password cocok dan masih menggunakan hash lama (sha256),
            // update ke hash yang lebih aman (bcrypt)
            if ($is_legacy_hash && hash_equals($stored_hash, hash('sha256', $password))) {
                $new_hash = password_hash($password, PASSWORD_DEFAULT);
                $update_stmt = $conn->prepare("UPDATE admin SET password = ? WHERE id = ?");
                $update_stmt->bind_param("si", $new_hash, $admin['id']);
                $update_stmt->execute();
            }

            // Set session dan redirect ke halaman utama admin
            $_SESSION['admin_logged_in'] = true;
            $_SESSION['admin_username'] = $username;
            header("Location: index.php");
            exit;
        } else {
            $error = "Username atau password salah!";
        }
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
        
        <?php if (!empty($error)):
            echo "<div class=\"login-error\">" . htmlspecialchars($error) . "</div>";
        endif; ?>
        
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
