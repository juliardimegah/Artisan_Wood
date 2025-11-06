<?php
session_start();
include './db_connect.php';

// Ambil ID produk dari URL
$id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Ambil data produk dari database
$stmt = $conn->prepare("SELECT * FROM products WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$product = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $product ? htmlspecialchars($product['name']) : 'Produk Tidak Ditemukan' ?> - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include './header.php'; ?>

<main class="container">
    <?php if ($product): ?>
        <div class="product-layout">
            <div class="product-gallery">
                <div class="main-image">
                    <!-- PERBAIKAN: Langsung gunakan URL dari database (ImgBB) -->
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
                </div>
                <div class="thumbnail-images">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product thumbnail">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product thumbnail">
                    <img src="<?= htmlspecialchars($product['image']) ?>" alt="Product thumbnail">
                </div>
            </div>

            <div class="product-info">
                <h1><?= htmlspecialchars($product['name']) ?></h1>
                <div class="rating">
                    <i class="fas fa-star"></i>
                    <span>5.0</span>
                </div>
                <p class="description"><?= nl2br(htmlspecialchars($product['description'])) ?></p>
                
                <form id="cart-form" method="POST" action="cart.php">
                    <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                    <input type="hidden" id="buy-now" name="buy_now" value="0">

                    <div class="price-section">
                        <span class="price">Rp<?= number_format($product['price'], 0, ',', '.') ?></span>
                        <div class="quantity">
                            <label for="qty">QTY</label>
                            <input type="number" id="qty" name="quantity" value="1" min="1">
                        </div>
                    </div>

                    <div class="action-buttons">
                        <button type="submit" name="buy_it_now" class="btn btn-primary">Buy it now</button>
                        <button type="submit" name="add_to_cart" class="btn btn-secondary">Add to cart</button>
                    </div>
                </form>
            </div>
        </div>
    <?php else: ?>
        <div style="text-align:center; padding:50px;">
            <h2>Produk tidak ditemukan 😢</h2>
            <p>Produk yang kamu cari mungkin sudah tidak tersedia.</p>
            <a href="./index.php" class="btn btn-secondary">Kembali ke daftar produk</a>
        </div>
    <?php endif; ?>
</main>

<?php include './footer.php'; ?>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.getElementById('cart-form');
    const buyNowBtn = document.querySelector('button[name="buy_it_now"]');
    
    if (buyNowBtn) {
        buyNowBtn.addEventListener('click', function(e) {
            e.preventDefault();
            const buyNowInput = document.getElementById('buy-now');
            if (buyNowInput) {
                buyNowInput.value = '1';
            }
            form.submit();
        });
    }
});
</script>

</body>
</html>
