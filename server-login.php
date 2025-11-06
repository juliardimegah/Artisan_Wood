<?php
session_start();
include 'db_connect.php';

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $auth_header);

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'No token provided']);
    exit;
}

// IMPORTANT: You must configure your Firebase project ID
$firebase_project_id = 'your-firebase-project-id';

try {
    // Get the public keys from Google
    $keys_url = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
    $keys = json_decode(file_get_contents($keys_url), true);

    // Decode the token
    $decoded_token = null;
    foreach ($keys as $key) {
        try {
            $decoded_token = JWT::decode($token, new Key($key, 'RS256'));
            break; // Stop if decoding is successful
        } catch (Exception $e) {
            // Continue to the next key
        }
    }

    if (!$decoded_token) {
        throw new Exception("Invalid token");
    }

    // Verify the token claims
    if ($decoded_token->aud !== $firebase_project_id) {
        throw new Exception("Invalid audience");
    }
    if ($decoded_token->iss !== 'https://securetoken.google.com/' . $firebase_project_id) {
        throw new Exception("Invalid issuer");
    }

    $email = $decoded_token->email;

    // Check if the user exists in your database
    $stmt = $conn->prepare("SELECT id, username FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        echo json_encode(['status' => 'success', 'message' => 'User logged in']);
    } else {
        // If the user does not exist, create a new user record
        $username = explode('@', $email)[0]; // Create a username from the email
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        $dummy_password = password_hash(uniqid(), PASSWORD_BCRYPT); // Create a dummy password
        $stmt->bind_param("sss", $username, $email, $dummy_password);
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['username'] = $username;
            echo json_encode(['status' => 'success', 'message' => 'User created and logged in']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Could not create user']);
        }
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
