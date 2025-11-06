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
            <img src="assets/images/Gambar-6.png" alt="Furniture collection">
        </div>
    </section>

    <section class="trending container">
        <h2>Trending Today</h2>
        <div class="product-grid">
            <!-- Dynamic Products -->
            <?php
            $sql = "SELECT * FROM products";
            $result = $conn->query($sql);
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="product-card">';
                    echo '<a href="product.php?id=' . $row['id'] . '">';
                    echo '<img src="' . $row['image'] . '" alt="' . htmlspecialchars($row['name']) . '">';
                    echo '</a>';
                    echo '<div class="product-card-info">';
                    echo '<a href="product.php?id=' . $row['id'] . '">';
                    echo '<h3>' . htmlspecialchars($row['name']) . '</h3>';
                    echo '</a>';
                    echo '<p class="price">Rp' . number_format($row['price'], 0, ',', '.') . '</p>';
                    echo '<form action="cart.php" method="POST">';
                    echo '<input type="hidden" name="product_id" value="'. $row['id'] .'">';
                    echo '<input type="hidden" name="quantity" value="1">';
                    echo '<button type="submit" class="btn-action">add to cart</button>';
                    echo '</form>';
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
