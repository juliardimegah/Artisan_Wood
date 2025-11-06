<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id']) || !isset($_GET['order_id'])) {
    header("Location: index.php"); // Redirect if not logged in or no order specified
    exit;
}

$user_id = $_SESSION['user_id'];
$order_id = intval($_GET['order_id']);

// Fetch order details
$order_query = $conn->prepare("SELECT * FROM orders WHERE id = ? AND user_id = ?");
$order_query->bind_param("ii", $order_id, $user_id);
$order_query->execute();
$order = $order_query->get_result()->fetch_assoc();

if (!$order) {
    // Order not found or doesn't belong to the user
    // Using die() is not user-friendly. A proper error page would be better.
    die("Order not found.");
}

// Fetch order items
$items_query = $conn->prepare("
    SELECT p.name, p.image, oi.quantity, oi.price
    FROM order_items oi
    JOIN products p ON oi.product_id = p.id
    WHERE oi.order_id = ?
");
$items_query->bind_param("i", $order_id);
$items_query->execute();
$order_items = $items_query->get_result();

// BUG: This fetches a generic shipping address, not the one tied to this specific order.
// A better DB design would link orders to a specific shipping_address_id.
$shipping_query = $conn->prepare("SELECT * FROM shipping_address WHERE user_id = ?");
$shipping_query->bind_param("i", $user_id);
$shipping_query->execute();
$shipping_address = $shipping_query->get_result()->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Confirmation - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <div class="confirmation-container">
        <h1><i class="fas fa-check-circle" style="color: #28a745;"></i> Thank You For Your Order!</h1>
        <p>Your order has been placed successfully. We've sent a confirmation to your email.</p>

        <div class="order-summary-card">
            <h2>Order Summary (ID: <?= $order['id'] ?>)</h2>
            <div class="order-details">
                <p><strong>Order Status:</strong> <?= htmlspecialchars($order['status']) ?></p>
                <p><strong>Estimated Delivery:</strong> <?= htmlspecialchars($order['delivery_est']) ?></p>
                <p><strong>Courier:</strong> <?= htmlspecialchars($order['courier']) ?></p>
            </div>

            <h3>Items Ordered</h3>
            <div class="ordered-items">
                <?php 
                $subtotal = 0;
                while ($item = $order_items->fetch_assoc()): 
                    $subtotal += $item['price'] * $item['quantity'];
                ?>
                <div class="order-item">
                    <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                    <div class="item-info">
                        <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                        <p>Qty: <?= $item['quantity'] ?></p>
                        <p>Rp<?= number_format($item['price'], 0, ',', '.') ?></p>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <h3>Shipping To</h3>
            <div class="shipping-info">
                <?php if ($shipping_address): ?>
                    <p><strong><?= htmlspecialchars($shipping_address['recipient_name']) ?></strong></p>
                    <p><?= htmlspecialchars($shipping_address['address']) ?>, <?= htmlspecialchars($shipping_address['city']) ?>, <?= htmlspecialchars($shipping_address['postal_code']) ?></p>
                    <p><?= htmlspecialchars($shipping_address['phone_number']) ?></p>
                <?php else: ?>
                    <p>No shipping address found.</p>
                <?php endif; ?>
            </div>

            <h3>Payment Summary</h3>
            <?php
            // BUG: Hardcoded fees. These should be retrieved from the order details for accuracy.
            $shipping_cost = 7000; // Example fixed shipping
            $admin_fee = 2500; // Example fixed admin fee
            $total_amount = $order['total_amount']; 
            ?>
            <div class="payment-details">
                <p><span>Subtotal:</span> <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span></p>
                <p><span>Shipping:</span> <span>Rp<?= number_format($shipping_cost, 0, ',', '.') ?></span></p>
                <p><span>Admin Fee:</span> <span>Rp<?= number_format($admin_fee, 0, ',', '.') ?></span></p>
                <hr>
                <p class="total"><span><strong>Total:</strong></span> <span><strong>Rp<?= number_format($total_amount, 0, ',', '.') ?></strong></span></p>
            </div>

            <div class="actions">
                <a href="index.php" class="btn-secondary">Continue Shopping</a>
                <a href="order.php" class="btn-primary">View Order History</a>
            </div>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
