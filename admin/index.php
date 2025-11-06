<?php
include '../db_connect.php';

// === Hitung Statistik Utama ===
$total_products = $conn->query("SELECT COUNT(*) AS total FROM products")->fetch_assoc()['total'];
$total_orders = $conn->query("SELECT COUNT(*) AS total FROM orders")->fetch_assoc()['total'];
$total_users = $conn->query("SELECT COUNT(*) AS total FROM users")->fetch_assoc()['total'];
$total_messages = $conn->query("SELECT COUNT(*) AS total FROM customer_messages")->fetch_assoc()['total'];

// === Ambil Data Tambahan ===
$recent_orders = $conn->query("SELECT id, user_id, total_amount, status, order_date FROM orders ORDER BY order_date DESC LIMIT 5");
$low_stock = $conn->query("SELECT id, name, stock FROM products WHERE stock < 10 ORDER BY stock ASC LIMIT 5");
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Artisan Wood CMS</title>
    
    <!-- CSS dan Font Awesome dipanggil di dalam <head> -->
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<?php 
// Header (navigasi) dimuat di awal <body>
include './header.php'; 
?>

<main class="admin-container">
    <h1><i class="fas fa-tachometer-alt"></i> Admin Dashboard</h1>

    <!-- Statistik Utama -->
    <div class="stats-grid">
        <div class="stat-card">
            <i class="fas fa-cube"></i>
            <h2><?= $total_products ?></h2>
            <p>Products</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-box"></i>
            <h2><?= $total_orders ?></h2>
            <p>Orders</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-users"></i>
            <h2><?= $total_users ?></h2>
            <p>Users</p>
        </div>
        <div class="stat-card">
            <i class="fas fa-envelope"></i>
            <h2><?= $total_messages ?></h2>
            <p>Messages</p>
        </div>
    </div>

    <!-- Grid Ringkasan -->
    <div class="summary-grid">
        <!-- Pesanan Terbaru -->
        <div class="summary-box">
            <h3><i class="fas fa-box-open"></i> Recent Orders</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>User</th><th>Total</th><th>Status</th><th>Date</th></tr>
                </thead>
                <tbody>
                <?php if ($recent_orders->num_rows > 0): ?>
                    <?php while ($r = $recent_orders->fetch_assoc()): ?>
                    <tr>
                        <td><?= $r['id'] ?></td>
                        <td><?= $r['user_id'] ?></td>
                        <td>Rp<?= number_format($r['total_amount'], 0, ',', '.') ?></td>
                        <td><span class="status <?= strtolower(str_replace(' ', '-', $r['status'])) ?>"><?= htmlspecialchars($r['status']) ?></span></td>
                        <td><?= date('d M Y', strtotime($r['order_date'])) ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="5"><em>No orders yet</em></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>

        <!-- Produk Stok Rendah -->
        <div class="summary-box">
            <h3><i class="fas fa-exclamation-triangle"></i> Low Stock Products</h3>
            <table>
                <thead>
                    <tr><th>ID</th><th>Product Name</th><th>Stock</th></tr>
                </thead>
                <tbody>
                <?php if ($low_stock->num_rows > 0): ?>
                    <?php while ($p = $low_stock->fetch_assoc()): ?>
                    <tr>
                        <td><?= $p['id'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= $p['stock'] ?></td>
                    </tr>
                    <?php endwhile; ?>
                <?php else: ?>
                    <tr><td colspan="3"><em>All stocks are healthy</em></td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<?php include './footer.php'; ?>
</body>
</html>
