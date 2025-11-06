<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Artisan Wood - Eco-friendly Furniture</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<?php include 'header.php'; ?>

<main>
    <section class="hero">
        <div class="hero-text">
            <h1>Find what you want here</h1>
            <p>Artisan Wood is a platform that provides accessories made from wood powder to beautify your room.</p>
            <a href="#" class="btn-shop">SHOP NOW</a>
        </div>
        <div class="hero-image">
            <img src="assets/Gambar-6.png" alt="Furniture collection">
        </div>
    </section>

    <section class="trending container">
        <h2>Trending Today</h2>
        <div class="product-grid">
            <!-- Custom Product (Static) -->
            <div class="product-card">
                <img src="assets/Gambar-1.png" alt="Custom Product">
                <div class="product-card-info">
                    <h3>Custom</h3>
                    <p class="price">Rp???</p>
                    <a href="custom.php" class="btn-action">Chat admin</a>
                </div>
            </div>

            <!-- Dynamic Products -->
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '<div class="product-card-info">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '<p class="price">Rp' . number_format($row['price'], 0, ',', '.') . '</p>';
                    echo '<button class="btn-action">add to cart</button>';
                    echo '</div></div>';
                }
            } else {
                echo "<p>No products found.</p>";
            }
            ?>
        </div>
    </section>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
