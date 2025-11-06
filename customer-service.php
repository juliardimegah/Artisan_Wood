    <?php include 'db_connect.php';
include 'header.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Customer Service - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>  

<main class="container">
    <h1>Customer Service</h1>

    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= isset($_SESSION['name']) ? htmlspecialchars($_SESSION['name']) : 'Guest'; ?></span>
            </div>
            <nav>
                <ul>
                    <li><a href="profile.php">My Profile</a></li>
                    <li><a href="order.php">My Order</a></li>
                    <li class="active"><a href="customer-service.php">Customer Service</a></li>
                </ul>
            </nav>
        </aside>

        <section class="content">
            <h2>Drop us a line</h2>

            <form class="contact-form" method="POST" action="">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" placeholder="Name" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Email" required>
                </div>

                <div class="form-group">
                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="8" placeholder="Write your message" required></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Submit</button>
                </div>
            </form>

            <?php
            // Handle form submission (optional)
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $name = htmlspecialchars($_POST['name']);
                $email = htmlspecialchars($_POST['email']);
                $message = htmlspecialchars($_POST['message']);

                $stmt = $conn->prepare("INSERT INTO customer_messages (name, email, message) VALUES (?, ?, ?)");
                $stmt->bind_param("sss", $name, $email, $message);
                if ($stmt->execute()) {
                    echo "<p class='success-message'>Thank you! Your message has been sent successfully.</p>";
                } else {
                    echo "<p class='error-message'>Oops! Something went wrong. Please try again later.</p>";
                }
                $stmt->close();
            }
            ?>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
