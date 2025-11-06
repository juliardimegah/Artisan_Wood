<?php
session_start();
include 'db_connect.php';

// Pastikan path absolut untuk keamanan dan konsistensi
if (!isset($_SESSION['user_id'])) {
    header("Location: /signin.php"); 
    exit;
}
$user_id = $_SESSION['user_id'];

// Proses form saat pengguna menekan "Confirm and pay"
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_payment'])) {
    $recipient_name = $_POST['name'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $postal_code = $_POST['postal_code'];
    $phone_number = $_POST['phone'];
    $courier = $_POST['shipping_method'];

    // Simpan atau perbarui alamat pengiriman untuk penggunaan di masa depan
    $stmt_addr = $conn->prepare("INSERT INTO shipping_address (user_id, recipient_name, address, city, postal_code, phone_number) VALUES (?, ?, ?, ?, ?, ?) ON DUPLICATE KEY UPDATE recipient_name=VALUES(recipient_name), address=VALUES(address), city=VALUES(city), postal_code=VALUES(postal_code), phone_number=VALUES(phone_number)");
    $stmt_addr->bind_param("isssss", $user_id, $recipient_name, $address, $city, $postal_code, $phone_number);
    $stmt_addr->execute();

    $total_amount = $_POST['total_amount'];
    $delivery_est = "Est. delivery : " . date('M d', strtotime('+3 days')) . " - " . date('M d', strtotime('+6 days'));

    $stmt_order = $conn->prepare("INSERT INTO orders (user_id, total_amount, status, delivery_est, courier) VALUES (?, ?, 'Belum Dibayar', ?, ?)");
    $stmt_order->bind_param("idss", $user_id, $total_amount, $delivery_est, $courier);
    $stmt_order->execute();
    $order_id = $conn->insert_id;

    $cart_items_query = $conn->prepare("SELECT product_id, quantity FROM cart WHERE user_id = ?");
    $cart_items_query->bind_param("i", $user_id);
    $cart_items_query->execute();
    $cart_items = $cart_items_query->get_result();

    $stmt_oi = $conn->prepare("INSERT INTO order_items (order_id, product_id, quantity, price) VALUES (?, ?, ?, ?)");
    $stmt_prod_price = $conn->prepare("SELECT price FROM products WHERE id = ?");

    while ($item = $cart_items->fetch_assoc()) {
        $stmt_prod_price->bind_param("i", $item['product_id']);
        $stmt_prod_price->execute();
        $product = $stmt_prod_price->get_result()->fetch_assoc();
        $stmt_oi->bind_param("iiid", $order_id, $item['product_id'], $item['quantity'], $product['price']);
        $stmt_oi->execute();
    }

    $stmt_clear_cart = $conn->prepare("DELETE FROM cart WHERE user_id = ?");
    $stmt_clear_cart->bind_param("i", $user_id);
    $stmt_clear_cart->execute();

    header("Location: /order_confirmation.php?order_id=" . $order_id);
    exit;
}

// --- Logika untuk Mengambil Alamat ---
$addr_query = $conn->prepare("SELECT recipient_name, address, city, postal_code, phone_number FROM shipping_address WHERE user_id = ?");
$addr_query->bind_param("i", $user_id);
$addr_query->execute();
$address_data = $addr_query->get_result()->fetch_assoc();

if (!$address_data) {
    $user_query = $conn->prepare("SELECT name, address, city, postal_code, phone FROM users WHERE id = ?");
    $user_query->bind_param("i", $user_id);
    $user_query->execute();
    $user_data = $user_query->get_result()->fetch_assoc();
    if ($user_data) {
        $address_data = [
            'recipient_name' => $user_data['name'],
            'address' => $user_data['address'],
            'city' => $user_data['city'],
            'postal_code' => $user_data['postal_code'],
            'phone_number' => $user_data['phone']
        ];
    }
}

// Ambil item keranjang belanja
$cart_query = $conn->prepare("SELECT p.name, p.price, p.image, c.quantity FROM cart c JOIN products p ON c.product_id = p.id WHERE c.user_id = ?");
$cart_query->bind_param("i", $user_id);
$cart_query->execute();
$cart_items = $cart_query->get_result();

$subtotal = 0;
$total_items = 0;
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Checkout</h1>
    <form class="checkout-layout" method="POST">
        <div class="checkout-details">
            <div class="payment-shipping">
                <div class="payment-method">
                    <h2>Pay with</h2>
                    <label><input type="radio" name="payment_method" value="COD" required> <i class="fas fa-money-bill-wave"></i> COD</label>
                    <label><input type="radio" name="payment_method" value="Bank account"> <i class="fas fa-university"></i> Bank account</label>
                    <label><input type="radio" name="payment_method" value="E-wallet"> <i class="fas fa-wallet"></i> E-wallet</label>
                </div>
                <div class="shipping-method">
                    <h2>Ship with</h2>
                    <label><input type="radio" name="shipping_method" value="Regular" required> Regular</label>
                    <label><input type="radio" name="shipping_method" value="Instan"> Instan</label>
                    <label><input type="radio" name="shipping_method" value="Same day"> Same day</label>
                </div>
            </div>
            <hr>
            <div class="review-order-ship-to">
                <div class="review-order">
                    <h2>Review order</h2>
                     <?php while($item = $cart_items->fetch_assoc()):
                        $subtotal += $item['price'] * $item['quantity'];
                        $total_items += $item['quantity'];
                    ?>
                    <div class="order-item">
                        <img src="<?= '/' . ltrim(htmlspecialchars($item['image']), '/') ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-info">
                            <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                            <p><strong>Rp<?= number_format($item['price'], 0, ',', '.') ?></strong></p>
                            <div class="quantity">QTY <?= $item['quantity'] ?></div>
                        </div>
                    </div>
                    <?php endwhile; $cart_items->data_seek(0); ?>
                </div>
                <div class="ship-to">
                    <h2>Ship to</h2>
                    <input type="text" name="name" placeholder="Name" value="<?= htmlspecialchars($address_data['recipient_name'] ?? '') ?>" required>
                    <input type="text" name="address" placeholder="Street address" value="<?= htmlspecialchars($address_data['address'] ?? '') ?>" required>
                    <input type="text" name="city" placeholder="City" value="<?= htmlspecialchars($address_data['city'] ?? '') ?>" required>
                    <input type="text" name="postal_code" placeholder="ZIP code" value="<?= htmlspecialchars($address_data['postal_code'] ?? '') ?>" required>
                    <input type="text" name="phone" placeholder="Phone number" value="<?= htmlspecialchars($address_data['phone_number'] ?? '') ?>" required>
                </div>
            </div>
        </div>

        <?php
        $shipping_cost = ($subtotal > 0) ? 7000 : 0;
        $admin_fee = ($subtotal > 0) ? 2500 : 0;
        $order_total = $subtotal + $shipping_cost + $admin_fee;
        ?>
        <div class="order-summary">
            <h2>Order Summary</h2>
            <input type="hidden" name="total_amount" value="<?= $order_total ?>">
            <ul>
                <li><span>Item(<?= $total_items ?>)</span><span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span></li>
                <li><span>Shipping</span><span>Rp<?= number_format($shipping_cost, 0, ',', '.') ?></span></li>
                <li><span>Admin fee</span><span>Rp<?= number_format($admin_fee, 0, ',', '.') ?></span></li>
            </ul>
            <div class="total">
                <span>Order Total</span>
                <span>Rp<?= number_format($order_total, 0, ',', '.') ?></span>
            </div>
            <p class="terms">With this purchase you agree to the terms and conditions.</p>
            <button type="submit" name="confirm_payment" class="btn-confirm <?= ($total_items > 0) ? '' : 'disabled' ?>">Confirm and pay</button>
        </div>
    </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
