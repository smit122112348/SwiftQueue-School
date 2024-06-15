<?php
// This file is used to connect to the database
// Database connection
$userName = "smit";
$password = "smit";
$server = 'localhost:3309';
$dbName = 'SwiftQueueDB';

try {
    // Create connection
    $conn = new PDO("mysql:host=$server;dbname=$dbName", $userName, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conn;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    return null;
}
?>
