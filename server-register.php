<?php
session_start();
include 'db_connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $firstName = $_POST['first-name'] ?? '';
    $lastName = $_POST['last-name'] ?? '';
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';

    if (empty($firstName) || empty($email) || empty($password)) {
        header("Location: create-account.php?error=" . urlencode("Please fill in all required fields."));
        exit;
    }

    if (strlen($password) < 6) {
        header("Location: create-account.php?error=" . urlencode("Password must be at least 6 characters."));
        exit;
    }

    $name = $firstName . ' ' . $lastName;

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        header("Location: create-account.php?error=" . urlencode("This email is already registered."));
        exit;
    } else {
        $hashed_password = password_hash($password, PASSWORD_BCRYPT);

        $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $name, $email, $hashed_password);
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['name'] = $name;
            header("Location: index.php");
            exit;
        } else {
            header("Location: create-account.php?error=" . urlencode("Could not create user. Please try again."));
            exit;
        }
    }

    $stmt->close();
    $conn->close();
} else {
    header("Location: create-account.php");
    exit;
}
?>