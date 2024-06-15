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

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /editCourse?error=Invalid CSRF token");
                exit();
            }

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

    public function deleteCourse() {

        
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {

            $data = json_decode(file_get_contents('php://input'), true);

            // Validate CSRF token
            $headers = getallheaders();
            $csrf_token = $headers['X-CSRF-Token'] ?? '';

            if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
                http_response_code(403);
                echo json_encode(['message' => 'Invalid CSRF token']);
                exit();
            }

            if (!isset($_SESSION['user'])) {
                http_response_code(403);
                die('Unauthorized');
            }

            $courseId = $data['courseId'];

            $course = $this->course->deleteCourse($courseId);

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

            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /editCourse?error=Invalid CSRF token");
                exit();
            }

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

            $course = $this->course->editCourse($courseId, $courseName, $courseStatus, $courseDescription, $courseStartDate, $courseStartTime, $courseEndDate, $courseEndTime);

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
$controller = new CourseController($conn);

// Determine which function to call based on the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'newCourse') !== false) {
    $controller->addCourse();
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && strpos($_SERVER['REQUEST_URI'], 'deleteCourse') !== false) {
    $controller->deleteCourse();
} else if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['_method']) && $_POST['_method'] === 'PUT') {
    $controller->editCourse();
}
