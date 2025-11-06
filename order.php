<?php
session_start();
include 'db_connect.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];
$username = $_SESSION['username'] ?? 'User';

?>
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
                <span><?= htmlspecialchars($username) ?></span>
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
                // Fetch orders and their items for the logged-in user
                $stmt = $conn->prepare("
                    SELECT 
                        o.id AS order_id, o.created_at, o.status, o.total_amount,
                        p.name AS product_name, p.image AS product_image, 
                        oi.quantity, oi.price
                    FROM orders AS o
                    JOIN order_items AS oi ON o.id = oi.order_id
                    JOIN products AS p ON oi.product_id = p.id
                    WHERE o.user_id = ?
                    ORDER BY o.created_at DESC
                ");
                $stmt->bind_param("i", $user_id);
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                    // Group items by order ID
                    $orders = [];
                    while ($row = $result->fetch_assoc()) {
                        $orders[$row['order_id']]['details'] = [
                            'created_at' => $row['created_at'],
                            'status' => $row['status'],
                            'total_amount' => $row['total_amount']
                        ];
                        $orders[$row['order_id']]['items'][] = [
                            'product_name' => $row['product_name'],
                            'product_image' => $row['product_image'],
                            'quantity' => $row['quantity'],
                            'price' => $row['price']
                        ];
                    }

                    foreach ($orders as $order_id => $order) {
                        echo '<div class="order-group">';
                        echo '<h4>Order ID: ' . $order_id . ' | Status: ' . htmlspecialchars($order['details']['status']) . '</h4>';
                        foreach ($order['items'] as $item) {
                            echo '
                            <div class="order-item">
                                <div class="item-main-info">
                                    <span class="item-qty">'. $item['quantity'] .'x</span>
                                    <img src="'. htmlspecialchars($item['product_image']) .'" alt="Product">
                                    <div class="item-details">
                                        <p><strong>'. htmlspecialchars($item['product_name']) .'</strong></p>
                                    </div>
                                </div>
                                <div class="item-price">
                                    Rp'. number_format($item['price'] * $item['quantity'], 0, ',', '.') .'
                                </div>
                            </div>';
                        }
                        echo '<div class="order-total"><strong>Total: Rp'. number_format($order['details']['total_amount'], 0, ',', '.') .'</strong></div>';
                        echo '</div>';
                    }
                } else {
                    echo '<p class="no-orders">Belum ada pesanan.</p>';
                }
                $stmt->close();
                ?>
            </div>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
