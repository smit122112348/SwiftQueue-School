<?php
// This is the model file for Course which interacts with the courses table in the database
class Course{
    private $conn;
    private $table_name = "courses";
    
    public function __construct($con,$db){
        // Set the database connection for the model
        $this->conn = $con;
        $this->conn->exec("USE $db;");
    }

    public function getAllCourses(){
        // Get all courses from the database
        $sql = "SELECT * FROM $this->table_name";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt;
    }

    public function getCourseDetails($id){
        // Get a course by its ID
        $sql = "SELECT * FROM $this->table_name WHERE course_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function addCourse($name, $status, $description, $startDate, $startTime, $endDate, $endTime) {
        // Add a new course to the database
        $sql = "INSERT INTO $this->table_name (course_name, course_description, course_status, course_startDate, course_endDate) VALUES (:name, :description, :status, :startDate, :endDate)";
        $stmt = $this->conn->prepare($sql);
    
        $startDateTime = $startDate . ' ' . $startTime;
        $endDateTime = $endDate . ' ' . $endTime;
        if($description == "0" || !empty($description)){
            $descriptionValue = $description;
        }else{
            $descriptionValue = null;
        }
    
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $descriptionValue, PDO::PARAM_STR); // Explicitly set the data type
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':startDate', $startDateTime);
        $stmt->bindParam(':endDate', $endDateTime);
    
        return $stmt->execute();
    }

    public function editCourse($id, $name, $status, $description, $startDate, $startTime, $endDate, $endTime) {
        // Edit an existing course in the database
        $sql = "UPDATE $this->table_name SET course_name = :name, course_description = :description, course_status = :status, course_startDate = :startDate, course_endDate = :endDate WHERE course_id = :id";
        $stmt = $this->conn->prepare($sql);
    
        $startDateTime = $startDate . ' ' . $startTime;
        $endDateTime = $endDate . ' ' . $endTime;
        if($description == "0" || !empty($description)){
            $descriptionValue = $description;
        }else{
            $descriptionValue = null;
        }
    
        $stmt->bindParam(':id', $id);
        $stmt->bindParam(':name', $name);
        $stmt->bindParam(':description', $descriptionValue, PDO::PARAM_STR); // Explicitly set the data type
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':startDate', $startDateTime);
        $stmt->bindParam(':endDate', $endDateTime);
    
        return $stmt->execute();
    }
    
    public function deleteCourse($id) {
        // Delete a course from the database
        $sql = "DELETE FROM $this->table_name WHERE course_id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }
    
}

