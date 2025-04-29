<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json");

// Log incoming data for debugging
file_put_contents('debug.log', file_get_contents("php://input") . "\n", FILE_APPEND);

$raw = file_get_contents("php://input");
$data = json_decode($raw, true);

if (isset($data['data']) && is_array($data['data']) && !empty($data['data'])) {
    // Connect to MariaDB
    $conn = new mysqli("localhost", "pslab_user", "pslab123", "pslab");
    if ($conn->connect_error) {
        file_put_contents('debug.log', "Connection failed: " . $conn->connect_error . "\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
        exit;
    }

    // Prepare the SQL statement
    $stmt = $conn->prepare("INSERT INTO project (date, title, description) VALUES (?, ?, ?)");
    if (!$stmt) {
        file_put_contents('debug.log', "Prepare failed: " . $conn->error . "\n", FILE_APPEND);
        http_response_code(500);
        echo json_encode(["error" => "Prepare failed: " . $conn->error]);
        $conn->close();
        exit;
    }

    // Bind and execute for each row
    foreach ($data['data'] as $row) {
        if (count($row) >= 3) {
            $date = $row[0];
            $title = $row[1];
            $description = $row[2];
            
            $stmt->bind_param("sss", $date, $title, $description);
            if (!$stmt->execute()) {
                file_put_contents('debug.log', "Execute failed: " . $stmt->error . "\n", FILE_APPEND);
                http_response_code(500);
                echo json_encode(["error" => "Execute failed: " . $stmt->error]);
                $stmt->close();
                $conn->close();
                exit;
            }
        }
    }

    $stmt->close();
    $conn->close();
    http_response_code(200);
    echo json_encode(["message" => "Records inserted successfully"]);
} else {
    file_put_contents('debug.log', "Invalid data: " . print_r($data, true) . "\n", FILE_APPEND);
    http_response_code(400);
    echo json_encode(["error" => "No valid data received"]);
}
?>