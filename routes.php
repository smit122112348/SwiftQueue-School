<?php
    // Define the routes and their corresponding files for each request method
    $routes = [
        'GET' => [
            '/' => './views/home.php',
            '/login' => './views/login.php',
            '/newCourse' => './views/newCourse.php',
        ],
        'POST' => [
            '/login' => './controllers/UserController.php',
            '/logout' => './controllers/UserController.php',
            '/newCourse' => './controllers/CourseController.php',
        ],
        'PUT' => [
            '/data' => 'data_put.php'
        ],
        'DELETE' => [
            '/data' => 'data_delete.php'
        ]
    ];
    
    // Get the request method and path
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Check if the route exists for the given request method
    if (isset($routes[$requestMethod][$requestPath])) {
        // Include the corresponding file
        include $routes[$requestMethod][$requestPath];
    } else {
        // If the route does not exist, show a 404 error
        include './views/404.php';
    }
