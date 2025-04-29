<?php
$host = "localhost";
$user = "pslab_user";
$password = "pslab123"; // ✅ Make sure this password is correct
$dbname = "pslab"; // ✅ Your database name

$conn = new mysqli($host, $user, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    http_response_code(500); // optional: tell browser it's a server error
    echo json_encode([
        "success" => false,
        "message" => "Connection failed: " . $conn->connect_error
    ]);
    exit(); // ❗ stop script properly
}
?>
