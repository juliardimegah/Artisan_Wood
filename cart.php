<?php include 'db_connect.php'; ?>
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

            <!-- Example static item (replace with DB-driven loop later) -->
            <div class="cart-item">
                <img src="https://placehold.co/150x150/d3c1ae/8B4513?text=Item" alt="Product image">
                <div class="item-details">
                    <h2>Two-in-One Wood powder<br>Phone holder and pen case</h2>
                    <p class="item-id">id0990</p>
                    <div class="item-rating">
                        <i class="fas fa-star"></i> 5.0
                    </div>
                </div>
                <div class="item-quantity">
                    <label for="qty">QTY</label>
                    <input type="number" id="qty" value="1" min="1">
                </div>
                <div class="item-price">
                    <span>Rp15.000</span>
                </div>
                <div class="item-shipping">
                    <p>Shipping</p>
                    <p>Rp7.000</p>
                    <p class="returns">Free returns</p>
                    <button class="remove-btn">Remove</button>
                </div>
            </div>

            <!-- Future dynamic example:
            <?php
            $query = "SELECT * FROM cart_items";
            $result = $conn->query($query);
            while ($item = $result->fetch_assoc()) {
                echo '<div class="cart-item">';
                echo '<img src="'.$item['image'].'" alt="'.$item['name'].'">';
                echo '<div class="item-details">';
                echo '<h2>'.$item['name'].'</h2>';
                echo '<p class="item-id">ID: '.$item['id'].'</p>';
                echo '</div>';
                echo '<div class="item-price">Rp'.number_format($item['price'], 0, ',', '.').'</div>';
                echo '</div>';
            }
            ?>
            -->

        </div>

        <div class="order-summary">
            <ul>
                <li>
                    <span>Item(1)</span>
                    <span>Rp15.000</span>
                </li>
                <li>
                    <span>Shipping</span>
                    <span>Rp7.000</span>
                </li>
            </ul>
            <div class="subtotal">
                <span>Subtotal</span>
                <span>Rp22.000</span>
            </div>
            <a href="pembayaran.php" class="btn-checkout">Go to checkout</a>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
