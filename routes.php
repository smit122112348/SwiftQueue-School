<?php
    $routes = [
        'GET' => [
            '/' => './views/home.php',
            '/login' => './views/login.php',
            '/register' => './views/register.php',
            '/newCourse' => './views/newCourse.php',
            '/editCourse' => './views/editCourse.php',
        ],
        'POST' => [
            '/login' => './controllers/UserController.php',
            '/logout' => './controllers/UserController.php',
            '/newCourse' => './controllers/CourseController.php',
            '/register' => './controllers/UserController.php',
        ],
        'PUT' => [
            '/editCourse' => './controllers/CourseController.php',
        ],
        'DELETE' => [
            '/deleteCourse' => './controllers/CourseController.php'
        ]
    ];
    
    // Get the request method and path
    $requestMethod = $_SERVER['REQUEST_METHOD'];
    if ($requestMethod === 'POST' && isset($_POST['_method'])) {
        $requestMethod = $_POST['_method'];
    }
    $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
    
    // Check if the route exists for the given request method
    if (isset($routes[$requestMethod][$requestPath])) {
        // Include the corresponding file
        include $routes[$requestMethod][$requestPath];
    } else {
        // If the route does not exist, show a 404 error
        include './views/404.php';
    }
