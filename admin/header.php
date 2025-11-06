<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['admin'])) {
    header("Location: login.php");
    exit;
}

// Dapatkan nama file saat ini (misal: orders.php)
$current_page = basename($_SERVER['PHP_SELF']);
?>
<header class="admin-header">
    <div class="admin-logo">
        <h1>Artisan Wood CMS</h1>
    </div>

    <nav class="admin-nav">
        <ul>
            <li><a href="index.php" class="<?= $current_page === 'index.php' ? 'active' : '' ?>"><i class="fas fa-home"></i> Dashboard</a></li>
            <li><a href="products.php" class="<?= $current_page === 'products.php' ? 'active' : '' ?>"><i class="fas fa-cube"></i> Products</a></li>
            <li><a href="orders.php" class="<?= $current_page === 'orders.php' ? 'active' : '' ?>"><i class="fas fa-box"></i> Orders</a></li>
            <li><a href="users.php" class="<?= $current_page === 'users.php' ? 'active' : '' ?>"><i class="fas fa-users"></i> Users</a></li>
            <li><a href="messages.php" class="<?= $current_page === 'messages.php' ? 'active' : '' ?>"><i class="fas fa-envelope"></i> Messages</a></li>
            <li><a href="custom_chat.php" class="<?= $current_page === 'custom_chat.php' ? 'active' : '' ?>"><i class="fas fa-comments"></i> Custom Chats</a></li>
        </ul>
    </nav>

    <div class="admin-user">
        <span><i class="fas fa-user-shield"></i> <?= htmlspecialchars($_SESSION['admin']); ?></span>
        <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
    </div>
</header>
