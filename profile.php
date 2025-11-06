<?php
session_start();
include 'db_connect.php';

// If the user is not logged in, redirect to the login page
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email, address FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();
$stmt->close();

$username = $user['username'];
$email = $user['email'];
$address = $user['address'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_username = $_POST['username'];
    $new_email = $_POST['email'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    $new_address = $_POST['address'];

    // Basic validation
    if ($new_password !== $confirm_password) {
        $error_message = "Passwords do not match.";
    } else {
        // Build the query
        $params = [];
        $types = "";
        $sql = "UPDATE users SET username=?, email=?, address=?";
        $params[] = $new_username;
        $types .= "sss";
        $params[] = $new_email;
        $params[] = $new_address;

        if (!empty($new_password)) {
            $sql .= ", password=?";
            $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
            $params[] = $hashed_password;
            $types .= "s";
        }

        $sql .= " WHERE id=?";
        $params[] = $user_id;
        $types .= "i";

        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);

        if ($stmt->execute()) {
            $success_message = "Profile updated successfully!";
            // Refresh user data
            $username = $new_username;
            $email = $new_email;
            $address = $new_address;
        } else {
            $error_message = "Failed to update profile. Please try again.";
        }
        $stmt->close();
    }
}
?>
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
                <span><?= htmlspecialchars($username) ?></span>
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

            <?php if (isset($success_message)): ?>
                <p class='success-message'><?= $success_message ?></p>
            <?php endif; ?>
            <?php if (isset($error_message)): ?>
                <p class='error-message'><?= $error_message ?></p>
            <?php endif; ?>

            <form class="profile-form" method="POST" action="">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" placeholder="Change username" value="<?= htmlspecialchars($username) ?>">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" placeholder="Change email" value="<?= htmlspecialchars($email) ?>">
                </div>

                <div class="form-group">
                    <label for="new-password">New password</label>
                    <input type="password" id="new-password" name="new_password" placeholder="Leave blank to keep current password">
                </div>

                <div class="form-group">
                    <label for="confirm-password">Confirm password</label>
                    <input type="password" id="confirm-password" name="confirm_password" placeholder="Confirm new password">
                </div>

                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address" rows="4" placeholder="Your address"><?= htmlspecialchars($address) ?></textarea>
                    <button type="button" class="btn-edit">Edit</button>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-save">Save</button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
