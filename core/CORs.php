<?php
$allowedOrigins = [  // allowed origins.
    'http://localhost',
    'http://35.212.226.199',
    'http://www.shoeseller.site',
    'https://www.shoeseller.site'
];

// Check if the Origin header is set and is allowed.
if (isset($_SERVER['HTTP_ORIGIN']) && in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
} else {
    // Optionally, you can choose to deny or allow a default origin.
    header("Access-Control-Allow-Origin: https://example.com");
}

// Allow specific HTTP methods.
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");

// Allow specific headers (adjust as necessary).
header("Access-Control-Allow-Headers: Content-Type, Authorization");

// Optionally, allow credentials if needed.
// header("Access-Control-Allow-Credentials: true");

// Handle OPTIONS preflight requests.
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    // Return a 200 OK response and exit.
    header("HTTP/1.1 200 OK");
    exit();
}
