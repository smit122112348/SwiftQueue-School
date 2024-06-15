<?php
// Database connection
$username = "smit";
$password = "smit";
$server = 'localhost:3309';
$dbname = 'SwiftQueueDB';

try {
    // Create connection
    $conn = new PDO("mysql:host=$server;dbname=$dbname", $username, $password);
    // set the PDO error mode to exception
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Code to create table "users" if it does not exist
    $sql = "CREATE TABLE IF NOT EXISTS users (
        user_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        user_full_name VARCHAR(50) NOT NULL,
        user_email VARCHAR(50) NOT NULL UNIQUE,
        user_password VARCHAR(50) NOT NULL,
        user_type ENUM('admin', 'basic', 'blocked') DEFAULT 'basic'
    )";
    $conn->exec($sql);

    // Code to create table "courses" if it does not exist
    $sql = "CREATE TABLE IF NOT EXISTS courses (
        course_id INT(6) UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        course_name VARCHAR(50) NOT NULL,
        course_description TEXT,
        course_startDate DATETIME NOT NULL,
        course_endDate DATETIME NOT NULL,
        course_status ENUM('Active', 'Inactive') DEFAULT 'Inactive',
        user_id INT(6) UNSIGNED,
        FOREIGN KEY (user_id) REFERENCES users(user_id)
    )";
    $conn->exec($sql);

    // Code to add initial data to the table "users"
    // Check if the table is empty, if empty add the initial data
        $stmt = $conn->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return $conn;
        }
        else{
                $password = crypt('admin', 'some_salt_30');
    $sql = "INSERT INTO users (user_full_name, user_email, user_password, user_type) 
            VALUES ('John Doe','admin@admin.com', '$password', 'admin')";
    $conn->exec($sql);

    $password = crypt('teacher', 'some_salt_30');
    $sql = "INSERT INTO users (user_full_name, user_email, user_password, user_type)
            VALUES ('Alice White','123@gmail.com', '$password', 'basic')";
    $conn->exec($sql);
        }

    

    // Code to add initial data to the table "courses"
    // Check if the table is empty, if empty add the initial data
        $stmt = $conn->query("SELECT COUNT(*) FROM courses");
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return $conn;
        }
        else{
                $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
            VALUES ('Object Oriented Programming', null, '2021-01-01 00:00:00', '2025-01-01 00:00:00', 'Active')";
    $conn->exec($sql);

    $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
            VALUES ('JavaScript', null, '2021-01-01 00:00:00', '2023-01-01 00:00:00', 'Inactive')";
    $conn->exec($sql);

    $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
            VALUES ('PHP', null, '2021-01-01 00:00:00', '2024-01-01 00:00:00', 'Active')";
    $conn->exec($sql);
        }

    // Return the connection for further use
    return $conn;
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
    return null;
}
?>
