<?php
// This file is used to handle course-related requests
session_start();
require_once dirname(__DIR__) . '/models/Course.php';
$config = require dirname(__DIR__) . '/config.php';

class CourseController {
    private $course;

    public function __construct($con, $db) {
        $this->course = new Course($con, $db);
    }

    public function addCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /editCourse?error=Invalid CSRF token");
                exit();
            }

            // Check if the user is logged in
            if (!isset($_SESSION['user'])) {
                http_response_code(403);
                die('Unauthorized');
            }

            $courseName = $_POST['course-name'];
            $courseDescription = $_POST['course-description'];
            $courseStatus = $_POST['course-status'];
            $courseStartDate = $_POST['course-start-date'];
            $courseStartTime = $_POST['course-start-time'];
            $courseEndDate = $_POST['course-end-date'];
            $courseEndTime = $_POST['course-end-time'];

            // Add the course to the database
            $course = $this->course->addCourse($courseName, $courseStatus, $courseDescription, $courseStartDate, $courseStartTime, $courseEndDate, $courseEndTime);

            // Redirect to the home page if the course was added successfully
            if ($course) {
                header("Location: /");
                exit();
            } else {
                header("Location: /newCourse?error=Failed to add course");
                exit();
            }
        }
    }

    public function deleteCourse() {        
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

            // Decode the JSON data
            $data = json_decode(file_get_contents('php://input'), true);

            // Validate CSRF token
            $headers = getallheaders();
            $csrf_token = $headers['X-CSRF-Token'] ?? '';

            if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
                http_response_code(403);
                echo json_encode(['message' => 'Invalid CSRF token']);
                exit();
            }

            // Check if the user is logged in
            if (!isset($_SESSION['user'])) {
                http_response_code(403);
                die('Unauthorized');
            }

            $courseId = $data['courseId'];

            // Delete the course from the database
            $course = $this->course->deleteCourse($courseId);

            // Return a 204 status code if the course was deleted successfully
            if ($course) {
                http_response_code(204);
                exit();
            } else {
                http_response_code(500);
                exit();
            }
        }
    }
    
    public function editCourse() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /editCourse?error=Invalid CSRF token");
                exit();
            }

            // Check if the user is logged in
            if (!isset($_SESSION['user'])) {
                http_response_code(403);
                die('Unauthorized');
            }

            $courseId = $_POST['course-id'];
            $courseName = $_POST['course-name'];
            $courseDescription = $_POST['course-description'];
            $courseStatus = $_POST['course-status'];
            $courseStartDate = $_POST['course-start-date'];
            $courseStartTime = $_POST['course-start-time'];
            $courseEndDate = $_POST['course-end-date'];
            $courseEndTime = $_POST['course-end-time'];

            // Edit the course in the database
            $course = $this->course->editCourse($courseId, $courseName, $courseStatus, $courseDescription, $courseStartDate, $courseStartTime, $courseEndDate, $courseEndTime);

            // Redirect to the home page if the course was edited successfully
            if ($course) {
                header("Location: /");
                exit();
            } else {
                header("Location: /editCourse?error=Failed to edit course");
                exit();
            }
        }
    }
}

$conn = require dirname(__DIR__) . '/db.php';
$controller = new CourseController($conn, $config['DB_NAME']);

// Determine which function to call based on the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'newCourse') !== false) {
    $controller->addCourse();
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && strpos($_SERVER['REQUEST_URI'], 'deleteCourse') !== false) {
    $controller->deleteCourse();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $controller->editCourse();
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && ($_SERVER['REQUEST_URI'] === '/') !== false) {
    include dirname(__DIR__) . '/views/home.php';
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'newCourse') !== false) {
    include dirname(__DIR__) . '/views/newCourse.php';
} else if ($_SERVER['REQUEST_METHOD'] === 'GET' && strpos($_SERVER['REQUEST_URI'], 'editCourse') !== false) {
    include dirname(__DIR__) . '/views/editCourse.php';
}
