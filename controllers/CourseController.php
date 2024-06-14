<?php
session_start();
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/models/Course.php';

class CourseController {
    private $course;

    public function __construct($db) {
        $this->course = new Course($db);
    }

    public function addCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $courseName = $_POST['course-name'];
            $courseDescription = $_POST['course-description'];
            $courseStatus = $_POST['course-status'];
            $courseStartDate = $_POST['course-start-date'];
            $courseStartTime = $_POST['course-start-time'];
            $courseEndDate = $_POST['course-end-date'];
            $courseEndTime = $_POST['course-end-time'];

            $course = $this->course->addCourse($courseName, $courseStatus, $courseDescription, $courseStartDate, $courseStartTime, $courseEndDate, $courseEndTime);

            if ($course) {
                header("Location: /");
                exit();
            } else {
                header("Location: /newCourse?error=Failed to add course");
                exit();
            }
        }
    }
}

$conn = require dirname(__DIR__) . '/db.php';
$controller = new CourseController($conn);

// Determine which function to call based on the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'newCourse') !== false) {
    $controller->addCourse();
}
