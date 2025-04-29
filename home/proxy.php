<?php
// Set headers to allow all origins and return JSON
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST');
header('Access-Control-Allow-Headers: Content-Type');

// The Google Apps Script web app URL
$url = 'https://script.google.com/macros/s/AKfycbyJMK1H0gHB4NNWCvCO7bcM3EKStIFBOSz8cA3-iEjZVrZJ8DlMiixMCkqyfcX6Uu-8/exec';

// Handle the request based on the method
$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    // Forward GET request to Apps Script
    $response = file_get_contents($url);
    if ($response === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch data from Apps Script']);
        exit;
    }
    echo $response;
} elseif ($method === 'POST') {
    // Forward POST request to Apps Script
    $data = file_get_contents('php://input');
    $options = [
        'http' => [
            'method' => 'POST',
            'header' => 'Content-Type: application/x-www-form-urlencoded',
            'content' => $data,
        ],
    ];
    $context = stream_context_create($options);
    $response = file_get_contents($url, false, $context);
    if ($response === false) {
        http_response_code(500);
        echo json_encode(['error' => 'Failed to fetch data from Apps Script']);
        exit;
    }
    echo $response;
} else {
    // Unsupported method
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}
?>