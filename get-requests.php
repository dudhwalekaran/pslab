<?php
include 'db.php';
header('Content-Type: application/json');

// Enable error logging
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

try {
    $result = $conn->query("SELECT id, name, email, status FROM request_login");
    if (!$result) {
        error_log("Query failed: " . $conn->error);
        echo json_encode([]);
        exit;
    }

    $requests = [];
    while ($row = $result->fetch_assoc()) {
        $requests[] = $row;
    }
    echo json_encode($requests);
} catch (Exception $e) {
    error_log("Exception: " . $e->getMessage());
    echo json_encode([]);
}
exit;
?>