<?php

class Course{
    private $conn;
    private $table_name = "courses";
    private $course_id;
    private $course_name;
    private $course_description;
    private $course_startDate;
    private $course_endDate;
    private $course_status;
    
    public function __construct($db){
        $this->conn = $db;
    }

    public function getCourseDetails($id){
        $sql = "SELECT * FROM $this->table_name WHERE course_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $this->course_id = $row['course_id'];
        $this->course_name = $row['course_name'];
        $this->course_description = $row['course_description'];
        $this->course_startDate = $row['course_startDate'];
        $this->course_endDate = $row['course_endDate'];
        $this->course_status = $row['course_status'];
    }

    public function getAllCourses(){
        $sql = "SELECT * FROM $this->table_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function addCourse($name, $status, $description, $startDate, $startTime, $endDate, $endTime) {
        $sql = "INSERT INTO $this->table_name (course_name, course_description, course_status, course_startDate, course_endDate) VALUES (:name, :description, :status, :startDate, :endDate)";
        $stmt = $this->conn->prepare($sql);
    
        $startDateTime = $startDate . ' ' . $startTime;
        $endDateTime = $endDate . ' ' . $endTime;
        $descriptionValue = $description ? $description : null;
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $descriptionValue);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':startDate', $startDateTime);
        $stmt->bindParam(':endDate', $endDateTime);
    
        return $stmt->execute();
    }
    
    
}

