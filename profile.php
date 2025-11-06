<?php
// Mulai sesi dan sertakan file koneksi
session_start();
include 'db_connect.php';

// Pastikan pengguna sudah login, jika tidak, arahkan ke halaman sign-in
if (!isset($_SESSION['user_id'])) {
    header("Location: /signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data lengkap pengguna, termasuk field alamat baru
$stmt = $conn->prepare("SELECT name, email, address, city, postal_code, phone FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Sertakan header setelah logika pengambilan data
include 'header.php';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - Artisan Wood</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;700&family=Playfair+Display:wght@700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="/assets/css/style.css"> <!-- Path absolut -->
</head>
<body>

<main class="container">
    <h1>My Profile</h1>

    <div class="profile-layout">
        <aside class="sidebar">
            <div class="user-info">
                <i class="fas fa-user-circle"></i>
                <!-- Pastikan variabel $user ada sebelum digunakan -->
                <span><?= htmlspecialchars($user['name'] ?? 'User'); ?></span>
            </div>
            <nav>
                <ul>
                    <!-- Gunakan path absolut untuk konsistensi -->
                    <li class="active"><a href="/profile.php">My Profile</a></li>
                    <li><a href="/order.php">My Order</a></li>
                    <li><a href="/customer-service.php">Customer Service</a></li>
                </ul>
            </nav>
        </aside>

        <section class="content">
            <h2>Profile Information</h2>
            
            <!-- Tampilkan pesan sukses jika ada -->
            <?php if (isset($_GET['status']) && $_GET['status'] == 'success'): ?>
                <div class="alert success">Profil berhasil diperbarui!</div>
            <?php endif; ?>

            <!-- Form diubah agar sesuai dengan checkout dan menunjuk ke update-profile.php -->
            <form class="contact-form" method="POST" action="update-profile.php">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" id="name" name="name" value="<?= htmlspecialchars($user['name'] ?? ''); ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?= htmlspecialchars($user['email'] ?? ''); ?>" required>
                </div>
                
                <hr>
                <h2>Shipping Address</h2>

                <div class="form-group">
                    <label for="address">Street Address</label>
                    <input type="text" id="address" name="address" placeholder="e.g., Jl. Jend. Sudirman No. 5" value="<?= htmlspecialchars($user['address'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="city">City</label>
                    <input type="text" id="city" name="city" placeholder="e.g., Jakarta" value="<?= htmlspecialchars($user['city'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="postal_code">ZIP / Postal Code</label>
                    <input type="text" id="postal_code" name="postal_code" placeholder="e.g., 12190" value="<?= htmlspecialchars($user['postal_code'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="phone">Phone Number</label>
                    <input type="text" id="phone" name="phone" placeholder="e.g., 081234567890" value="<?= htmlspecialchars($user['phone'] ?? ''); ?>">
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn-submit">Save Changes</button>
                </div>
            </form>
        </section>
    </div>
</main>

<?php include 'footer.php'; ?>

</body>
</html>
