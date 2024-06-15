<?php

class Seeder{
    private $conn;
    
    public function __construct($db){
        $this->conn = $db;
    }

    public function seedDatabase(){
        $this->seedUsers();
        $this->seedCourses();
    }

    public function makeTables(){
        $sql = "CREATE TABLE IF NOT EXISTS users (
            user_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            user_full_name VARCHAR(100) NOT NULL,
            user_email VARCHAR(100) NOT NULL,
            user_password VARCHAR(255) NOT NULL,
            user_type ENUM('admin', 'basic') NOT NULL
        )";
        $this->conn->exec($sql);

        $sql = "CREATE TABLE IF NOT EXISTS courses (
            course_id INT(11) AUTO_INCREMENT PRIMARY KEY,
            course_name VARCHAR(100) NOT NULL,
            course_description TEXT,
            course_startDate DATETIME NOT NULL,
            course_endDate DATETIME NOT NULL,
            course_status ENUM('Active', 'Inactive') NOT NULL
        )";
        $this->conn->exec($sql);
    }

    public function seedUsers(){
        $stmt = $this->conn->query("SELECT COUNT(*) FROM users");
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return $this->conn;
        }
        else{
                $password = crypt('admin', 'some_salt_30');
        $sql = "INSERT INTO users (user_full_name, user_email, user_password, user_type) 
                VALUES ('John Doe','admin@admin.com', '$password', 'admin')";
        $this->conn->exec($sql);

        $password = crypt('teacher', 'some_salt_30');
        $sql = "INSERT INTO users (user_full_name, user_email, user_password, user_type)
                VALUES ('Alice White','123@gmail.com', '$password', 'basic')";
        $this->conn->exec($sql);
        }
    }

    public function seedCourses(){
        $stmt = $this->conn->query("SELECT COUNT(*) FROM courses");
        $count = $stmt->fetchColumn();
        if ($count > 0) {
            return $this->conn;
        }
        else{
                $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
            VALUES ('Object Oriented Programming', null, '2021-01-01 00:00:00', '2025-01-01 00:00:00', 'Active')";
        $this->conn->exec($sql);

        $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
                VALUES ('JavaScript', null, '2021-01-01 00:00:00', '2023-01-01 00:00:00', 'Inactive')";
        $this->conn->exec($sql);

        $sql = "INSERT INTO courses (course_name, course_description, course_startDate, course_endDate, course_status)
                VALUES ('PHP', null, '2021-01-01 00:00:00', '2024-01-01 00:00:00', 'Active')";
        $this->conn->exec($sql);
        }

    }

}

