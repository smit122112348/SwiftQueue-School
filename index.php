<?php
    // This file is the entry point of the application
    require './Seeder.php';

    // Create a new Seeder object and seed the database
    $conn = require './db.php';
    $seeder = new Seeder($conn);
    $seeder->makeTables();
    $seeder->seedDatabase();

    // Route the request to the appropriate file
    $routes = [
        'GET' => [
            '/' => './views/home.php',
            '/login' => './views/login.php',
            '/register' => './views/register.php',
            '/newCourse' => './views/newCourse.php',
            '/editCourse' => './views/editCourse.php',
            '/userDetails' => './views/userDetails.php',
        ],
        'POST' => [
            '/login' => './controllers/UserController.php',
            '/logout' => './controllers/UserController.php',
            '/newCourse' => './controllers/CourseController.php',
            '/register' => './controllers/UserController.php',
            '/makeAdmin' => './controllers/UserController.php',
            '/userAccess' => './controllers/UserController.php',
        ],
        'PUT' => [
            '/editCourse' => './controllers/CourseController.php',
        ],
        'DELETE' => [
            '/deleteCourse' => './controllers/CourseController.php',
            '/deleteAccount' => './controllers/UserController.php',
        ]
    ];
    
    // Get the request method and path
    $requestMethod = $_SERVER['REQUEST_METHOD'];

    // Check if the request method is POST and the _method parameter is set for 'PUT' or 'DELETE' requests
    if ($requestMethod === 'POST' && isset($_POST['_method'])) {
        $requestMethod = $_POST['_method'];
    }

    // Get the request path
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Check if the route exists for the given request method
    if (isset($routes[$requestMethod][$requestPath])) {
        // Include the corresponding file
        include $routes[$requestMethod][$requestPath];
    } else {
        // If the route does not exist, show a 404 error
        include './views/404.php';
    }
