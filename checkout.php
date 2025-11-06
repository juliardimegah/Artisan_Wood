<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>Checkout</h1>
    <div class="checkout-layout">
        <div class="checkout-details">

            <!-- Payment & Shipping Section -->
            <div class="payment-shipping">
                <div class="payment-method">
                    <h2>Pay with</h2>
                    <label><input type="radio" name="payment"> <i class="fas fa-money-bill-wave"></i> COD</label>
                    <label><input type="radio" name="payment"> <i class="fas fa-university"></i> Bank account</label>
                    <label><input type="radio" name="payment"> <i class="fas fa-wallet"></i> E-wallet</label>
                </div>

                <div class="shipping-method">
                    <h2>Ship with</h2>
                    <label><input type="radio" name="shipping"> Regular</label>
                    <label><input type="radio" name="shipping"> Instan</label>
                    <label><input type="radio" name="shipping"> Same day</label>
                </div>
            </div>

            <hr>

            <!-- Review Order & Address -->
            <div class="review-order-ship-to">
                <div class="review-order">
                    <h2>Review order</h2>
                    <div class="order-item">
                        <img src="https://placehold.co/100x100/d3c1ae/8B4513?text=Item" alt="Product">
                        <div class="item-info">
                            <p><strong>Two-in-One Wood powder<br>Phone holder and pen case</strong></p>
                            <p>id0990</p>
                            <p><strong>Rp15.000</strong></p>
                            <div class="quantity">QTY 1</div>
                            <p class="delivery-info">
                                Est. delivery : Jan 10 - Jan 13<br>
                                Jnt Express<br>
                                Shipping : Rp7.000
                            </p>
                        </div>
                    </div>
                </div>

                <div class="ship-to">
                    <h2>Ship to</h2>
                    <form class="address-form">
                        <input type="text" placeholder="Name">
                        <input type="text" placeholder="Street address">
                        <input type="text" placeholder="City">
                        <input type="text" placeholder="State/Province/Region">
                        <input type="text" placeholder="ZIP code">
                        <input type="text" placeholder="Phone number">
                        <button type="button" class="btn-add">Add</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="order-summary">
            <h2>Order Summary</h2>
            <ul>
                <li><span>Item(1)</span><span>Rp15.000</span></li>
                <li><span>Shipping</span><span>Rp7.000</span></li>
                <li><span>Admin fee</span><span>Rp2.500</span></li>
            </ul>
            <div class="total">
                <span>Order Total</span>
                <span>Rp24.500</span>
            </div>
            <p class="terms">With this purchase you agree to the terms and conditions.</p>
            <button class="btn-confirm">Confirm and pay</button>
        </div>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
