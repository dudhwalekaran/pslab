<?php
header('Content-Type: application/json');

session_start();
require_once 'db.php'; // Include the database connection

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

if (empty($email) || empty($password)) {
    echo json_encode(["success" => false, "message" => "All fields are required."]);
    exit;
}

if (!$conn) {
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

$query = "SELECT id, email, password FROM users WHERE email = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc();
    if (password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        echo json_encode(["success" => true, "message" => "Login Successful!", "redirect" => "home/dashboard.html"]);
        exit;
    } else {
        echo json_encode(["success" => false, "message" => "Invalid email or password."]);
        exit;
    }
}

echo json_encode(["success" => false, "message" => "Invalid email or password."]);
$stmt->close();
$conn->close();
exit;
?>