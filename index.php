<?php
// This file is the entry point of the application

// Include CORS handling
require 'cors.php';

@session_start();

require './Seeder.php';

$conn = require './db.php';
$config = require './config.php';

// Check if the database exists
$dbCheckQuery = $conn->query("SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = '" . $config['DB_NAME'] . "'");
$dbExists = $dbCheckQuery->fetchColumn();
$dbCheckQuery->closeCursor(); // Close the cursor to free up the connection for the next query
$seeder = new Seeder($conn);
if ($dbExists === false) {
    // Create the database since it does not exist
    $seeder->createDatabase();
    $conn->exec("USE " . $config['DB_NAME']);
    
    // This runs only once to create the database and tables
    
    $seeder->makeTables();
    $seeder->seedDatabase();
} else {
    // Use the database
    $conn->exec("USE " . $config['DB_NAME']);

    // Check if the 'users' table exists
    $checkQuery = $conn->query("SHOW TABLES LIKE 'users'");
    $tableExists = $checkQuery->fetchColumn();
    $checkQuery->closeCursor(); // Close the cursor to free up the connection for the next query

    if ($tableExists === false) {
        // This runs only once to create the tables and seed the database if the tables do not exist
        $seeder->makeTables();
        $seeder->seedDatabase();
    }
}


$user_controller = './controllers/UserController.php';
$course_controller = './controllers/CourseController.php';

// Route the request to the appropriate file
$routes = [
    'GET' => [
        '/' => $course_controller,
        '/login' => $user_controller,
        '/register' => $user_controller,
        '/newCourse' => $course_controller,
        '/editCourse' => $course_controller,
        '/userDetails' => $user_controller,
    ],
    'POST' => [
        '/login' => $user_controller,
        '/logout' => $user_controller,
        '/newCourse' => $course_controller,
        '/register' => $user_controller,
        '/makeAdmin' => $user_controller,
        '/userAccess' => $user_controller,
    ],
    'PUT' => [
        '/editCourse' => $course_controller,
    ],
    'DELETE' => [
        '/deleteCourse' => $course_controller,
        '/deleteAccount' => $user_controller,
        '/deleteUser' => $user_controller
    ],
    'OPTIONS' => []
];

// Get the request method and path
$requestMethod = $_SERVER['REQUEST_METHOD'];

// Check if the request method is POST and the _method parameter is set for 'PUT' or 'DELETE' requests
if ($requestMethod === 'POST' && isset($_POST['_method'])) {
    $requestMethod = $_POST['_method'];
}

// Get the request path
$requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// Handle OPTIONS requests for CORS preflight
if ($requestMethod === 'OPTIONS') {
    http_response_code(204); // No Content
    exit();
}

// Check if the route exists for the given request method
if (isset($routes[$requestMethod][$requestPath])) {
    // Include the corresponding file
    include $routes[$requestMethod][$requestPath];
} else {
    // If the route does not exist, show a 404 error
    include './views/404.php';
}
