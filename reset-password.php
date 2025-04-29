<?php
ob_start();
include 'db.php';
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

error_log("Starting reset-password.php");

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
    exit;
}

$newPassword = filter_input(INPUT_POST, 'new-password', FILTER_SANITIZE_STRING);
$confirmPassword = filter_input(INPUT_POST, 'confirm-password', FILTER_SANITIZE_STRING);
$token = filter_input(INPUT_GET, 'token', FILTER_SANITIZE_STRING);

if (empty($newPassword) || empty($confirmPassword) || empty($token)) {
    error_log("Missing fields: newPassword=$newPassword, confirmPassword=$confirmPassword, token=$token");
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if ($newPassword !== $confirmPassword) {
    error_log("Passwords do not match");
    echo json_encode(["success" => false, "message" => "Passwords do not match."]);
    exit;
}

// Basic password strength check
if (strlen($newPassword) < 8) {
    error_log("Password too short");
    echo json_encode(["success" => false, "message" => "Password must be at least 8 characters long."]);
    exit;
}

try {
    // Verify token
    $stmt = $conn->prepare("SELECT email, expires FROM password_reset WHERE token = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        error_log("Invalid token: $token");
        echo json_encode(["success" => false, "message" => "Invalid or expired token."]);
        exit;
    }

    $row = $result->fetch_assoc();
    $email = $row['email'];
    $expires = $row['expires'];
    $stmt->close();

    if (strtotime($expires) < time()) {
        error_log("Token expired for email: $email");
        echo json_encode(["success" => false, "message" => "Reset link has expired."]);
        exit;
    }

    // Update password
    $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
    $stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("ss", $hashedPassword, $email);
    if (!$stmt->execute()) {
        throw new Exception("Update failed: " . $stmt->error);
    }
    $stmt->close();

    // Delete token
    $stmt = $conn->prepare("DELETE FROM password_reset WHERE token = ?");
    if (!$stmt) {
        throw new Exception("Prepare failed: " . $conn->error);
    }
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $stmt->close();

    error_log("Password reset successful for email: $email");
    echo json_encode(["success" => true, "message" => "Password successfully reset."]);
} catch (Exception $e) {
    error_log("Error in reset-password.php: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Internal server error."]);
}

$conn->close();
ob_end_flush();
exit;