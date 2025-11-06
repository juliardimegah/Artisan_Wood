<?php
session_start();
include 'db_connect.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    // Menggunakan path absolut untuk redirect
    header('Location: /signin.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$name = $_SESSION['name'] ?? 'User'; // Mengambil nama dari session

$status_filter = $_GET['status'] ?? 'Semua';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <!-- Menggunakan path absolut untuk CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>My Order</h1>

    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($name) ?></span>
            </div>
            <nav>
                <ul>
                    <!-- Menggunakan path absolut untuk navigasi sidebar -->
                    <li><a href="/profile.php">My Profile</a></li>
                    <li class="active"><a href="/order.php">My Order</a></li>
                    <li><a href="/customer-service.php">Customer Service</a></li>
                </ul>
            </nav>
        </aside>

        <section class="content">
            <div class="order-tabs">
                <a href="#" data-status="Semua" class="tab-link active">Semua</a>
                <a href="#" data-status="Belum dibayar" class="tab-link">Belum dibayar</a>
                <a href="#" data-status="Sedang dikemas" class="tab-link">Sedang dikemas</a>
                <a href="#" data-status="Dikirim" class="tab-link">Dikirim</a>
                <a href="#" data-status="Selesai" class="tab-link">Selesai</a>
                <a href="#" data-status="Dibatalkan" class="tab-link">Dibatalkan</a>
            </div>

            <div class="order-list">
                <!-- Order items will be loaded here dynamically -->
            </div>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        const tabs = document.querySelectorAll('.tab-link');
        const orderList = document.querySelector('.order-list');

        function fetchOrders(status) {
            orderList.innerHTML = '<p>Loading orders...</p>';
            
            // Menggunakan path absolut untuk fetch request
            fetch(`/get_orders.php?status=${status}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.text();
                })
                .then(data => {
                    orderList.innerHTML = data;
                })
                .catch(error => {
                    console.error('Error fetching orders:', error);
                    orderList.innerHTML = '<p class="no-orders">Gagal memuat pesanan. Silakan periksa konsol untuk detailnya.</p>';
                });
        }

        tabs.forEach(tab => {
            tab.addEventListener('click', function(e) {
                e.preventDefault();
                tabs.forEach(t => t.classList.remove('active'));
                this.classList.add('active');
                const status = this.getAttribute('data-status');
                // Update URL for better navigation history
                history.pushState(null, '', `/order.php?status=${status}`);
                fetchOrders(status);
            });
        });

        // Load initial orders based on URL parameter or default to 'Semua'
        const initialStatus = new URLSearchParams(window.location.search).get('status') || 'Semua';
        document.querySelector(`.tab-link[data-status="${initialStatus}"]`).classList.add('active');
        fetchOrders(initialStatus);
    });
</script>

</body>
</html>
