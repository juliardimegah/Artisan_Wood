<?php
session_start();
include 'db_connect.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}
$user_id = $_SESSION['user_id'];

// Handle Add to Cart & Buy Now
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['product_id'])) {
    $product_id = intval($_POST['product_id']);
    $quantity = isset($_POST['quantity']) ? intval($_POST['quantity']) : 1;
    $is_buy_now = isset($_POST['buy_now']) && $_POST['buy_now'] == '1';

    // Check if product already in cart
    $stmt = $conn->prepare("SELECT * FROM cart WHERE user_id = ? AND product_id = ?");
    $stmt->bind_param("ii", $user_id, $product_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Update quantity
        $cart_item = $result->fetch_assoc();
        // If buying now, we replace the quantity. Otherwise, we add it.
        $new_quantity = $is_buy_now ? $quantity : $cart_item['quantity'] + $quantity;
        $update_stmt = $conn->prepare("UPDATE cart SET quantity = ? WHERE id = ?");
        $update_stmt->bind_param("ii", $new_quantity, $cart_item['id']);
        $update_stmt->execute();
    } else {
        // Insert new item
        $insert_stmt = $conn->prepare("INSERT INTO cart (user_id, product_id, quantity) VALUES (?, ?, ?)");
        $insert_stmt->bind_param("iii", $user_id, $product_id, $quantity);
        $insert_stmt->execute();
    }

    if ($is_buy_now) {
        header("Location: checkout.php");
    } else {
        header("Location: cart.php");
    }
    exit;
}

// Handle Remove from Cart
if (isset($_GET['remove'])) {
    $cart_id = intval($_GET['remove']);
    $stmt = $conn->prepare("DELETE FROM cart WHERE id = ? AND user_id = ?");
    $stmt->bind_param("ii", $cart_id, $user_id);
    $stmt->execute();
    header("Location: cart.php");
    exit;
}

// Fetch cart items for the user
$cart_query = $conn->prepare("
    SELECT c.id, p.name, p.price, p.image, c.quantity
    FROM cart c
    JOIN products p ON c.product_id = p.id
    WHERE c.user_id = ?
");
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
    <title>Shopping Cart - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Shopping cart</h1>

    <div class="cart-layout">
        <div class="cart-items">
            <?php if ($cart_items->num_rows > 0): ?>
                <?php while($item = $cart_items->fetch_assoc()): ?>
                    <?php
                    $item_total = $item['price'] * $item['quantity'];
                    $subtotal += $item_total;
                    $total_items += $item['quantity'];
                    ?>
                    <div class="cart-item">
                        <img src="<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>">
                        <div class="item-details">
                            <h2><?= htmlspecialchars($item['name']) ?></h2>
                            <p class="item-id">ID: PROD-<?= $item['id'] ?></p>
                        </div>
                        <div class="item-quantity">
                            <label>QTY</label>
                            <input type="number" value="<?= $item['quantity'] ?>" min="1" readonly>
                        </div>
                        <div class="item-price">
                            <span>Rp<?= number_format($item_total, 0, ',', '.') ?></span>
                        </div>
                        <div class="item-shipping">
                            <a href="?remove=<?= $item['id'] ?>" class="remove-btn">Remove</a>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <p>Your cart is empty.</p>
            <?php endif; ?>
        </div>

        <?php
        $shipping = ($subtotal > 0) ? 7000 : 0;
        $grand_total = $subtotal + $shipping;
        ?>
        <div class="order-summary">
            <ul>
                <li>
                    <span>Item(<?= $total_items ?>)</span>
                    <span>Rp<?= number_format($subtotal, 0, ',', '.') ?></span>
                </li>
                <li>
                    <span>Shipping</span>
                    <span>Rp<?= number_format($shipping, 0, ',', '.') ?></span>
                </li>
            </ul>
            <div class="subtotal">
                <span>Subtotal</span>
                <span>Rp<?= number_format($grand_total, 0, ',', '.') ?></span>
            </div>
            <a href="checkout.php" class="btn-checkout <?= ($subtotal > 0) ? '' : 'disabled' ?>">Go to checkout</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
