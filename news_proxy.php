<?php
// Show PHP errors
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Allow CORS requests from any origin
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Google Apps Script Web App URL
$googleAppsScriptUrl = "https://script.google.com/macros/s/AKfycbySwWC_5ZGZQK0fQL4LfoLJ7oozcLpROwa5vGpwosRkyPJOuwiWIPNODFJncz07WpQu/exec";

// Initialize cURL session
$ch = curl_init($googleAppsScriptUrl);

// Set cURL options
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true); 
curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Accept: application/json"
]);

// Execute the request
$response = curl_exec($ch);

// Check for cURL errors
if (curl_errno($ch)) {
    $error = curl_error($ch);
    curl_close($ch);
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => "cURL Error: $error"]);
    exit;
}

// Get HTTP status code
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Check if the request was successful
if ($httpCode !== 200) {
    http_response_code($httpCode);
    echo json_encode(['success' => false, 'message' => "HTTP Error: $httpCode"]);
    exit;
}

// Set the response content type
header("Content-Type: application/json");
echo $response;
?>
