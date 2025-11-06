<?php
include 'db_connect.php';
include 'header.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$stmt = $conn->prepare("SELECT name, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="./assets/css/style.css">
</head>
<body>

<main class="container">
    <h1>My Profile</h1>

    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span><?= htmlspecialchars($user['name']); ?></span>
            </div>
            <nav>
                <ul>
                    <li class="active"><a href="profile.php">My Profile</a></li>
                    <li><a href="order.php">My Order</a></li>
                    <li><a href="customer-service.php">Customer Service</a></li>
                </ul>
            </nav>
        </aside>

        <section class="content">
            <h2>Profile Information</h2>

            <form class="contact-form" method="POST" action="update-profile.php">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="4"><?= htmlspecialchars($user['address']); ?></textarea>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Update Profile</button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
