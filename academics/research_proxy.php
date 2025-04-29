<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $type = $_GET['type'] ?? 'Thesis'; // Default to Thesis if type is not specified
    $spreadsheetId = "1xZ28kAaGCgl89v15ooicu7nOd7PUWXeiYwLfyOdT1AA"; // Your spreadsheet ID

    // Determine the range based on the type
    if ($type === 'Thesis') {
        $range = 'Thesis!A2:D'; // Thesis sheet: Timestamp, Title, Author, Link
    } elseif ($type === 'Project') {
        $range = 'Project!A2:C'; // Project sheet: Timestamp, Title, Description
    } elseif ($type === 'Paper') {
        $range = 'Paper!A2:D'; // Paper sheet: (A: Timestamp), B: Title, C: Description, D: Link
    } elseif ($type === 'Achievement') {
        $range = 'Achievement!A2:C'; // Achievements sheet: (A: Timestamp), B: Title, C: Description
    } else {
        echo json_encode([]);
        exit;
    }

    $url = "https://sheets.googleapis.com/v4/spreadsheets/$spreadsheetId/values/$range?key=AIzaSyCFXR9w7XsIglULpKf3u8txyt_UvaYnXMc";
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $response = curl_exec($ch);
    curl_close($ch);

    if ($response) {
        $data = json_decode($response, true);
        $rows = $data['values'] ?? [];
        $result = [];

        if ($type === 'Thesis') {
            $result = array_map(function($row) {
                return [
                    'title' => $row[1] ?? '', // Column B: Title
                    'author' => $row[2] ?? '', // Column C: Author
                    'link' => $row[3] ?? '' // Column D: Link
                ];
            }, $rows);
        } elseif ($type === 'Project') {
            $result = array_map(function($row) {
                return [
                    'title' => $row[1] ?? '', // Column B: Title (skipping timestamp in A)
                    'description' => $row[2] ?? '', // Column C: Description
                    'image' => '' // No image column
                ];
            }, $rows);
        } elseif ($type === 'Paper') {
            $result = array_map(function($row) {
                return [
                    'title' => $row[1] ?? '', // Column B: Title (skipping timestamp in A)
                    'description' => $row[2] ?? '', // Column C: Description
                    'link' => $row[3] ?? '' // Column D: Link
                ];
            }, $rows);
        } elseif ($type === 'Achievement') {
            $result = array_map(function($row) {
                return [
                    'title' => $row[1] ?? '', // Column B: Title (skipping timestamp in A)
                    'description' => $row[2] ?? '' // Column C: Description
                ];
            }, $rows);
        }

        echo json_encode($result);
    } else {
        echo json_encode([]);
    }
}
?>