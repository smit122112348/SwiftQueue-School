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
}

