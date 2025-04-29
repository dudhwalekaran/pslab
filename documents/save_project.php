<?php
header("Content-Type: application/json");

// Database connection
$conn = new mysqli("localhost", "pslab_user", "pslab123", "pslab");

if ($conn->connect_error) {
    echo json_encode(["status" => "error", "message" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// Check if form data is received
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $docType = isset($_POST['docType']) ? $_POST['docType'] : '';
    $dateTime = isset($_POST['dateTime']) ? $_POST['dateTime'] : '';
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    $image_url = isset($_POST['image_url']) ? $_POST['image_url'] : '';
    $speaker = isset($_POST['speaker']) ? $_POST['speaker'] : '';
    $author = isset($_POST['author']) ? $_POST['author'] : '';
    $link = isset($_POST['link']) ? $_POST['link'] : '';

    // Validate required fields
    if (empty($docType) || empty($dateTime) || empty($title)) {
        echo json_encode(["status" => "error", "message" => "Document type, date/time, and title are required"]);
        exit;
    }

    // Prepare SQL based on document type
    $stmt = null;
    switch ($docType) {
        case 'news':
            if (empty($description)) {
                echo json_encode(["status" => "error", "message" => "Description is required for News"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO news (date, title, description, image_url, speaker) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("sssss", $dateTime, $title, $description, $image_url, $speaker);
            break;

        case 'achievement':
            if (empty($description)) {
                echo json_encode(["status" => "error", "message" => "Description is required for Achievement"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO achievement (date, title, description) VALUES (?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("sss", $dateTime, $title, $description);
            break;

        case 'project':
            if (empty($description)) {
                echo json_encode(["status" => "error", "message" => "Description is required for Project"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO project (date, title, description) VALUES (?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("sss", $dateTime, $title, $description);
            break;

        case 'article':
            if (empty($link)) {
                echo json_encode(["status" => "error", "message" => "Link is required for Article"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO article (time, title, author, link) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("ssss", $dateTime, $title, $author, $link);
            break;

        case 'thesis':
            if (empty($description) || empty($link)) {
                echo json_encode(["status" => "error", "message" => "Description and link are required for Thesis"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO thesis (time, title, author, description, link) VALUES (?, ?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("sssss", $dateTime, $title, $author, $description, $link);
            break;

        case 'paper':
            if (empty($description) || empty($link)) {
                echo json_encode(["status" => "error", "message" => "Description and link are required for Paper"]);
                exit;
            }
            $stmt = $conn->prepare("INSERT INTO paper (time, title, description, link) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                echo json_encode(["status" => "error", "message" => "Prepare failed: " . $conn->error]);
                $conn->close();
                exit;
            }
            $stmt->bind_param("ssss", $dateTime, $title, $description, $link);
            break;

        default:
            echo json_encode(["status" => "error", "message" => "Invalid document type"]);
            $conn->close();
            exit;
    }

    // Execute the statement
    if ($stmt->execute()) {
        $stmt->close();
        $conn->close();
        echo json_encode(["status" => "success", "message" => ucfirst($docType) . " saved successfully"]);
        exit;
    } else {
        $stmt->close();
        $conn->close();
        echo json_encode(["status" => "error", "message" => "Execute failed: " . $stmt->error]);
        exit;
    }
} else {
    echo json_encode(["status" => "error", "message" => "Invalid request method"]);
    exit;
}
?>