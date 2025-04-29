<?php
include 'db.php'; // Adjusted to local path assuming db.php is in /public_static/
header('Content-Type: application/json');

error_reporting(E_ALL);
ini_set('display_errors', 1); // Enable for debugging, revert to 0 after
ini_set('log_errors', 1);
ini_set('error_log', 'php_errors.log');

if (!isset($conn) || $conn === null) {
    error_log("Database connection not established in handle-request.php");
    echo json_encode(["success" => false, "message" => "Database connection failed."]);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $input = json_decode(file_get_contents('php://input'), true);
    $id = $input['id'] ?? '';
    $action = $input['action'] ?? '';

    if (empty($id) || !in_array($action, ['accept', 'reject', 'delete'])) {
        error_log("Invalid request: id=$id, action=$action");
        echo json_encode(["success" => false, "message" => "Invalid request."]);
        exit;
    }

    try {
        $conn->begin_transaction();

        $stmt = $conn->prepare("SELECT name, email, password, status FROM request_login WHERE id = ?");
        if (!$stmt) {
            error_log("Prepare failed: " . $conn->error);
            throw new Exception("Prepare failed: " . $conn->error);
        }
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $request = $result->fetch_assoc();
            $stmt->close();

            if ($action === 'accept') {
                $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
                $stmt->bind_param("s", $request['email']);
                $stmt->execute();
                if ($stmt->get_result()->num_rows === 0) {
                    $stmt = $conn->prepare("INSERT INTO users (name, email, password) VALUES (?, ?, ?)");
                    if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                    $stmt->bind_param("sss", $request['name'], $request['email'], $request['password']);
                    if (!$stmt->execute()) throw new Exception("Insert failed: " . $conn->error);
                    $stmt->close();
                }
                $stmt = $conn->prepare("UPDATE request_login SET status = 'accepted' WHERE id = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) throw new Exception("Update failed: " . $conn->error);
                $stmt->close();
            } elseif ($action === 'reject') {
                if ($request['status'] === 'accepted') {
                    $stmt = $conn->prepare("SELECT email FROM users WHERE email = ?");
                    $stmt->bind_param("s", $request['email']);
                    $stmt->execute();
                    if ($stmt->get_result()->num_rows > 0) {
                        $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
                        if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                        $stmt->bind_param("s", $request['email']);
                        if (!$stmt->execute()) throw new Exception("Delete failed: " . $conn->error);
                        $stmt->close();
                    }
                }
                $stmt = $conn->prepare("UPDATE request_login SET status = 'rejected' WHERE id = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) throw new Exception("Update failed: " . $conn->error);
                $stmt->close();
            } elseif ($action === 'delete') {
                $stmt = $conn->prepare("DELETE FROM request_login WHERE id = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("i", $id);
                if (!$stmt->execute()) throw new Exception("Delete failed: " . $conn->error);
                $stmt->close();

                $stmt = $conn->prepare("DELETE FROM users WHERE email = ?");
                if (!$stmt) throw new Exception("Prepare failed: " . $conn->error);
                $stmt->bind_param("s", $request['email']);
                $stmt->execute();
                $stmt->close();
            }

            $conn->commit();
            echo json_encode(["success" => true, "message" => ucfirst($action) . " action completed successfully."]);
        } else {
            $conn->rollback();
            error_log("Request not found for id: $id");
            echo json_encode(["success" => false, "message" => "Request not found."]);
        }
    } catch (Exception $e) {
        $conn->rollback();
        error_log("Error in handle-request.php: " . $e->getMessage());
        echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Method not allowed."]);
}
exit;
?>