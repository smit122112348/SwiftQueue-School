<?php
// cors.php

$config = require 'config.php';

$allowedOrigins = $config['ALLOWED_ORIGINS'];
$allowedMethods = $config['ALLOWED_METHODS'];
$allowedHeaders = $config['ALLOWED_HEADERS'];

if (isset($_SERVER['HTTP_ORIGIN'])) {
    if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
        header("Access-Control-Allow-Origin: " . $_SERVER['HTTP_ORIGIN']);
        header("Access-Control-Allow-Methods: $allowedMethods");
        header("Access-Control-Allow-Headers: $allowedHeaders");
        
        // Allow credentials if needed
        header("Access-Control-Allow-Credentials: true");
    } else {
        // Deny the request by not setting CORS headers and returning an error
        http_response_code(403);
        echo 'Origin not allowed';
        exit();
    }
}

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(204);
    exit();
}
