<?php
// This file is used to connect to the database
// Database connection
$config = require 'config.php';
$userName = $config['DB_USER'];
$password = $config['DB_PASS'];
$server = $config['DB_HOST'];

try {
    // Create connection
    $conn = new PDO("mysql:host=$server", $userName, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    return null;
}
?>
