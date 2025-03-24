<?php
require_once __DIR__ . '/../vendor/autoload.php';

// Load environment variables from the .env file
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();
require_once __DIR__ . '/../routes/web.php';
?>