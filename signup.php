<?php
include 'db.php';
header('Content-Type: application/json');

// Enable error reporting and logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

// Get form data safely
$name = $_POST['name'] ?? '';
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Log received data for debugging
error_log("Received: name=$name, email=$email, password=***");

if (empty($name) || empty($email) || empty($password)) {
    error_log("Validation failed: Missing fields");
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit();
}

// Hash the password securely
$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
error_log("Hashed password generated");

// Check for duplicate email in request_login
$stmt = $conn->prepare("SELECT email FROM request_login WHERE email = ?");
if (!$stmt) {
    error_log("Prepare failed (request_login): " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already requested."]);
    exit();
}
$stmt->close();

// Check for duplicate email in users
$stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
if (!$stmt) {
    error_log("Prepare failed (users): " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("s", $email);
$stmt->execute();
if ($stmt->get_result()->num_rows > 0) {
    echo json_encode(["success" => false, "message" => "Email already exists."]);
    exit();
}
$stmt->close();

// Insert into request_login with name
$stmt = $conn->prepare("INSERT INTO request_login (name, email, password, status) VALUES (?, ?, ?, 'pending')");
if (!$stmt) {
    error_log("Prepare failed (insert): " . $conn->error);
    echo json_encode(["success" => false, "message" => "Database error: " . $conn->error]);
    exit();
}
$stmt->bind_param("sss", $name, $email, $hashedPassword);
if ($stmt->execute()) {
    error_log("Signup successful for email: $email");
    echo json_encode(["success" => true, "message" => "Signup request submitted. Awaiting admin approval."]);
} else {
    error_log("Execute failed: " . $stmt->error);
    echo json_encode(["success" => false, "message" => "Database error: " . $stmt->error]);
}
$stmt->close();
exit();
?>