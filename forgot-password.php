<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/php-error.log');

error_log("Starting forgot-password.php");

require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

try {
    $conn = new mysqli('localhost', 'root', '', 'pslab');
    if ($conn->connect_error) {
        throw new Exception('Database connection failed: ' . $conn->connect_error);
    }
    error_log("Database connected");
} catch (Exception $e) {
    error_log("DB Connection Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit;
}

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    error_log("Invalid email: $email");
    echo json_encode(['success' => false, 'message' => 'Please enter a valid email address.']);
    exit;
}

try {
    // Check if email exists in users table
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if (!$stmt) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    error_log("Users table email check completed");

    if ($result->num_rows === 0) {
        // Email doesn't exist, return generic message for security
        error_log("Email not found in users table: $email");
        echo json_encode(['success' => true, 'message' => 'If the email exists, a reset link has been sent.']);
        exit;
    }
    $stmt->close();
} catch (Exception $e) {
    error_log("DB Query Error (users check): " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit;
}

try {
    $token = bin2hex(random_bytes(32));
    $expires = date('Y-m-d H:i:s', time() + 3600);
    
    // Check if email already has a reset token
    $check = $conn->prepare("SELECT id FROM password_reset WHERE email = ?");
    if (!$check) {
        throw new Exception('Prepare statement failed: ' . $conn->error);
    }
    $check->bind_param("s", $email);
    $check->execute();
    $checkResult = $check->get_result();
    
    if ($checkResult->num_rows > 0) {
        // Update existing record
        $update = $conn->prepare("UPDATE password_reset SET token = ?, expires = ? WHERE email = ?");
        if (!$update) {
            throw new Exception('Prepare update failed: ' . $conn->error);
        }
        $update->bind_param("sss", $token, $expires, $email);
        if (!$update->execute()) {
            throw new Exception('Update failed: ' . $update->error);
        }
        $update->close();
        error_log("Updated password_reset for email: $email");
    } else {
        // Insert new record
        $insert = $conn->prepare("INSERT INTO password_reset (email, token, expires) VALUES (?, ?, ?)");
        if (!$insert) {
            throw new Exception('Prepare insert failed: ' . $conn->error);
        }
        $insert->bind_param("sss", $email, $token, $expires);
        if (!$insert->execute()) {
            throw new Exception('Insert failed: ' . $insert->error);
        }
        $insert->close();
        error_log("Inserted new password_reset for email: $email");
    }
    $check->close();
} catch (Exception $e) {
    error_log("Token Error: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Internal server error.']);
    exit;
}

try {
    $resetLink = "http://localhost:8080/pslab_static/reset-password.html?token=$token";
    $mail = new PHPMailer(true);
    $mail->SMTPDebug = 2;
    $mail->Debugoutput = function($str, $level) {
        error_log("PHPMailer Debug [$level]: $str");
    };
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->Username = 'dudhwalekaran@gmail.com';
    $mail->Password = 'xwze exzv zzha ijwz';
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;
    $mail->setFrom('dudhwalekaran@gmail.com', 'PSLAB');
    $mail->addAddress($email);
    $mail->isHTML(true);
    $mail->Subject = 'Reset Your Password';
    $mail->Body = "Click <a href='$resetLink'>here</a> to reset your password. This link expires in 1 hour.";
    $mail->send();
    error_log("Email sent successfully to: $email");
    echo json_encode(['success' => true, 'message' => 'If the email exists, a reset link has been sent.']);
} catch (Exception $e) {
    error_log("Mailer Error: " . $mail->ErrorInfo);
    echo json_encode(['success' => false, 'message' => 'Failed to send email: ' . $mail->ErrorInfo]);
}

$conn->close();