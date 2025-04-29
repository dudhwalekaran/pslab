<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id'])) {
    header("Location: ../login.html");
    exit;
}

// Get the requested file
$file = isset($_GET['file']) ? basename($_GET['file']) : 'dashboard.html';
$filePath = __DIR__ . '/' . $file;

// Check if the file exists and is an HTML file
if (file_exists($filePath) && pathinfo($filePath, PATHINFO_EXTENSION) === 'html') {
    // Include the requested file
    include $filePath;
} else {
    // Redirect to login if file doesn't exist
    header("Location: ../login.html");
    exit;
}
?>