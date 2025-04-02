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

if (isset($_SESSION['user'])) {
    if ($_SESSION['user_agent'] !== $_SERVER['HTTP_USER_AGENT'] ||
        $_SESSION['ip_address'] !== $_SERVER['REMOTE_ADDR']) {
        // Potential hijacking detected, regenerate the session.
        session_regenerate_id(true);
    }
    if (isset($_SESSION['last_regenerated'])) {
        // Regenerate session ID every 30 minutes.
        if (time() - $_SESSION['last_regenerated'] > 1800) {
            session_regenerate_id(true);
            $_SESSION['last_regenerated'] = time();
        }
    } else {
        $_SESSION['last_regenerated'] = time();
    }
}