<?php include 'db_connect.php'; ?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="profile.css">
</head>
<body>

<?php include 'header.php'; ?>

<main class="container">
    <h1>My Profile</h1>
    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <span>Nama User</span>
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
            <h2>Manage Profile</h2>

            <form class="profile-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Change username">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Change email">
                </div>

                <div class="form-group">
                    <label for="new-password">New password</label>
                    <input type="password" id="new-password" name="new_password" placeholder="Change password">
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm password">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="4" placeholder="Your address"></textarea>
                    <button type="button" class="btn-edit">Edit</button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>

            <?php
            // Handle form submission
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                $username = htmlspecialchars($_POST['username']);
                $email = htmlspecialchars($_POST['email']);
                $new_password = htmlspecialchars($_POST['new_password']);
                $confirm_password = htmlspecialchars($_POST['confirm_password']);
                $address = htmlspecialchars($_POST['address']);

                if ($new_password === $confirm_password) {
                    $stmt = $conn->prepare("UPDATE users SET username=?, email=?, password=?, address=? WHERE id=?");
                    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
                    $user_id = 1; // Example static user ID; replace with logged-in user session ID later
                    $stmt->bind_param("ssssi", $username, $email, $hashed_password, $address, $user_id);
                    if ($stmt->execute()) {
                        echo "<p class='success-message'>Profile updated successfully!</p>";
                    } else {
                        echo "<p class='error-message'>Failed to update profile. Please try again.</p>";
                    }
                    $stmt->close();
                } else {
                    echo "<p class='error-message'>Passwords do not match.</p>";
                }
            }
            ?>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
