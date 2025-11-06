<?php
session_start();
include 'db_connect.php';

require_once 'vendor/autoload.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

// Get the token from the Authorization header
$auth_header = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = str_replace('Bearer ', '', $auth_header);

if (!$token) {
    http_response_code(401);
    echo json_encode(['error' => 'No token provided']);
    exit;
}

// Get user data from the request body
$request_body = json_decode(file_get_contents('php://input'), true);
$displayName = $request_body['displayName'] ?? '';


// IMPORTANT: You must configure your Firebase project ID
$firebase_project_id = 'your-firebase-project-id';

try {
    // Get the public keys from Google to verify the token
    $keys_url = 'https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com';
    $keys = json_decode(file_get_contents($keys_url), true);

    // Decode the token
    $decoded_token = null;
    foreach ($keys as $key) {
        try {
            // Use the public key to decode the JWT
            $decoded_token = JWT::decode($token, new Key($key, 'RS256'));
            break; 
        } catch (Exception $e) {
            // Try the next key
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
    $username = $displayName ?: explode('@', $email)[0];

    // Check if user already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        http_response_code(409); // Conflict
        echo json_encode(['error' => 'User already exists']);
    } else {
        // Insert new user into the database
        $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        // We store a dummy password as authentication is handled by Firebase
        $dummy_password = password_hash(uniqid(), PASSWORD_BCRYPT);
        $stmt->bind_param("sss", $username, $email, $dummy_password);
        
        if ($stmt->execute()) {
            $new_user_id = $conn->insert_id;
            // Create a session for the new user
            $_SESSION['user_id'] = $new_user_id;
            $_SESSION['username'] = $username;
            echo json_encode(['status' => 'success', 'message' => 'User created and logged in']);
        } else {
            http_response_code(500);
            echo json_encode(['error' => 'Could not create user in database']);
        }
    }

    $stmt->close();
} catch (Exception $e) {
    http_response_code(401);
    echo json_encode(['error' => $e->getMessage()]);
}
