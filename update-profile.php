<?php
session_start();
include 'db_connect.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: signin.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $address = $_POST['address'] ?? '';

    if (empty($name) || empty($email)) {
        header("Location: profile.php?error=" . urlencode("Name and email are required."));
        exit;
    }

    $stmt = $conn->prepare("UPDATE users SET name = ?, email = ?, address = ? WHERE id = ?");
    $stmt->bind_param("sssi", $name, $email, $address, $user_id);

    if ($stmt->execute()) {
        $_SESSION['name'] = $name; // Update session name
        header("Location: profile.php?success=" . urlencode("Profile updated successfully."));
        exit;
    } else {
        header("Location: profile.php?error=" . urlencode("Could not update profile. Please try again."));
        exit;
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: profile.php");
    exit;
}
?>