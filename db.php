<?php

$username = "smit";
$password = "smit";
$server = 'localhost:3309';
$db = 'SwiftQueueDB';

    $conn = new PDO("mysql:host=$server;dbname=$db", $username, $password);

    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    echo "Connected successfully";

?>