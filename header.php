<?php 
// Memulai sesi hanya jika belum ada sesi yang aktif
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<head> 
    <!-- Menggunakan path absolut untuk CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css">
    <style>
        .account {
            position: relative;
        }

        /* Gaya yang disederhanakan untuk tombol profil */
        .user-actions .account > a {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 25px; /* Bentuk pil */
            text-decoration: none;
            color: #333;
            background-color: #f0f0f0;
            border: 1px solid #ddd;
            font-weight: 500;
            font-size: 14px;
        }

        /* Efek hover dan semua gaya dropdown telah dihapus */

    </style>
</head>
<header class="main-header">
    <!-- Menggunakan path absolut untuk navigasi logo -->
    <div class="logo" onclick="window.location.href='/index.php'">ARTISAN WOOD</div>
    <div class="search-bar">
        <i class="fas fa-search"></i>
        <input type="text" placeholder="Search what you need">
    </div>
    <div class="user-actions">
        <?php if (isset($_SESSION['user_id'])) : 
            $firstName = explode(' ', htmlspecialchars($_SESSION['name']))[0];
        ?>
            <div class="account">
                <!-- Link ini sekarang menjadi tautan langsung ke halaman profil -->
                <a href="./profile.php">
                    <i class="fas fa-user"></i>
                    <span>Hello, <?= $firstName; ?></span>
                </a>
                <!-- Dropdown telah dihapus sepenuhnya -->
            </div>
        <?php else : ?>
            <!-- Gaya tombol Sign In tidak berubah -->
            <a href="./signin.php" class="account">
                <i class="fas fa-user"></i>
                <span>Sign In<br>ACCOUNT</span>
            </a>
        <?php endif; ?>
        <!-- Menggunakan path absolut untuk cart -->
        <a href="./cart.php" class="cart">
            <i class="fas fa-shopping-cart"></i>
        </a>
    </div>
</header>
