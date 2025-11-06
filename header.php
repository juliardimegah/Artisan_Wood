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
        .account .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 120px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            right: 0; 
        }
        .account:hover .dropdown-content {
            display: block;
        }
        .dropdown-content a {
            color: black;
            padding: 10px 15px;
            text-decoration: none;
            display: block;
            text-align: left;
        }
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
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
                <a href="#">
                    <i class="fas fa-user"></i>
                    <span>Hello, <?= $firstName; ?></span>
                </a>
                <div class="dropdown-content">
                    <!-- Menggunakan path absolut untuk profile dan logout -->
                    <a href="./profile.php">Profile</a>
                    <a href="./logout.php">Logout</a>
                </div>
            </div>
        <?php else : ?>
            <!-- Menggunakan path absolut untuk signin -->
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
