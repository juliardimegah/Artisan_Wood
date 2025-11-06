<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Order - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>My Order</h1>

    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>Nama User</span>
            </div>
            <nav>
                <ul>
                    <li><a href="profile.php">My Profile</a></li>
                    <li class="active"><a href="order.php">My Order</a></li>
                    <li><a href="customer-service.php">Customer Service</a></li>
                </ul>
            </nav>
        </aside>

        <section class="content">
            <div class="order-tabs">
                <a href="#" class="active">Semua</a>
                <a href="#">Belum dibayar</a>
                <a href="#">Sedang dikemas</a>
                <a href="#">Dikirim</a>
                <a href="#">Selesai</a>
                <a href="#">Dibatalkan</a>
            </div>

            <div class="order-list">
                <?php
                // Example of fetching data from local MySQL database
                $query = "SELECT * FROM orders ORDER BY created_at DESC";
                $result = $conn->query($query);

                if ($result && $result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        echo '
                        <div class="order-item">
                            <div class="item-main-info">
                                <span class="item-qty">'.$row['quantity'].'x</span>
                                <img src="'.$row['image'].'" alt="Product">
                                <div class="item-details">
                                    <p><strong>'.$row['product_name'].'</strong></p>
                                    <p class="delivery-details">Delivery</p>
                                    <p class="delivery-details">Est. delivery : '.$row['delivery_est'].'</p>
                                    <p class="delivery-details">'.$row['courier'].'</p>
                                </div>
                            </div>
                            <div class="item-price">
                                Rp'.number_format($row['price'], 0, ',', '.').'
                            </div>
                        </div>';
                    }
                } else {
                    echo '<p class="no-orders">Belum ada pesanan.</p>';
                }
                ?>
            </div>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
