<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: /signin.php"); 
    exit;
}
$user_id = $_SESSION['user_id'];

// ... (logika proses order dan pengambilan alamat) ...

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
    <!-- ... (head content) ... -->
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Checkout</h1>
    <form class="checkout-layout" method="POST">
        <div class="checkout-details">
            <!-- ... (payment and shipping) ... -->
            <hr>
            <div class="review-order-ship-to">
                <div class="review-order">
                    <h2>Review order</h2>
                     <?php while($item = $cart_items->fetch_assoc()):
                        $subtotal += $item['price'] * $item['quantity'];
                        $total_items += $item['quantity'];
                    ?>
                    <div class="order-item">
                        <!-- PERBAIKAN: Langsung gunakan URL dari database (ImgBB) -->
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-info">
                            <p><strong><?= htmlspecialchars($item['name']) ?></strong></p>
                            <p><strong>Rp<?= number_format($item['price'], 0, ',', '.') ?></strong></p>
                            <div class="quantity">QTY <?= $item['quantity'] ?></div>
                        </div>
                    </div>
                    <?php endwhile; $cart_items->data_seek(0); ?>
                </div>
                <!-- ... (ship-to form) ... -->
            </div>
        </div>

        <div class="order-summary">
            <!-- ... (order summary content) ... -->
        </div>
    </form>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
