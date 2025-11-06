<?php
session_start();
include 'db_connect.php';

// Pastikan pengguna sudah login dan metodenya adalah POST
if (!isset($_SESSION['user_id']) || $_SERVER["REQUEST_METHOD"] != "POST") {
    // Redirect ke halaman signin jika tidak memenuhi syarat
    header("Location: /signin.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil semua data dari form
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$address = $_POST['address'] ?? '';
$city = $_POST['city'] ?? '';
$postal_code = $_POST['postal_code'] ?? '';
$phone = $_POST['phone'] ?? '';

// Validasi dasar: nama dan email tidak boleh kosong
if (empty($name) || empty($email)) {
    // Arahkan kembali dengan pesan error jika validasi gagal
    header("Location: /profile.php?status=error&message=" . urlencode("Name and email are required."));
    exit;
}

// Siapkan query UPDATE untuk semua field, termasuk field alamat yang baru
$stmt = $conn->prepare(
    "UPDATE users SET name = ?, email = ?, address = ?, city = ?, postal_code = ?, phone = ? WHERE id = ?"
);

// Bind semua parameter ke query
$stmt->bind_param("ssssssi", $name, $email, $address, $city, $postal_code, $phone, $user_id);

// Eksekusi query dan berikan feedback
if ($stmt->execute()) {
    // Perbarui juga nama di sesi agar langsung tampil di header
    $_SESSION['name'] = $name;
    // Arahkan kembali ke profil dengan pesan sukses
    header("Location: /profile.php?status=success");
    exit;
} else {
    // Arahkan kembali dengan pesan error jika query gagal
    header("Location: /profile.php?status=error&message=" . urlencode("Failed to update profile. Please try again."));
    exit;
}

$stmt->close();
$conn->close();
?>
