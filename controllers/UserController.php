<?php
// This file is used to handle user-related requests
@session_start();
require_once dirname(__DIR__) . '/models/User.php';
$config = require dirname(__DIR__) . '/config.php';
class UserController {
    private $user;

    public function __construct($con, $db) {
        $this->user = new User($con, $db);
    }

    private function validatePassword($password) {
        // Validate the password
        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = "at least 8 characters long";
        }
        if (!preg_match('/\d/', $password)) {
            $errors[] = "at least one number";
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = "at least one uppercase letter";
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = "at least one lowercase letter";
        }
        if (!preg_match('/\W/', $password)) {
            $errors[] = "at least one special character";
        }
        return $errors;
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /login?error=Invalid CSRF token");
                exit();
            }

            $email = $_POST['user_email'];
            $password = $_POST['user_password'];

            // Login the user
            $user = $this->user->login($email, $password);

            // Redirect to the home page if the user is logged in successfully
            if ($user) {

                // Check if the user is blocked
                if ($user['user_type'] == 'blocked') {
                    header("Location: /login?error=Account is blocked");
                    exit();
                }

                $_SESSION['user'] = $user;
                header("Location: /");
                exit();
            } else {
                header("Location: /login?error=Invalid email or password");
                exit();
            }
        }
    }

    public function logout() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

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

            // Logout the user
            session_start();
            session_unset();
            session_destroy();
            header("Location: /login");
            exit();
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {

            // Validate CSRF token
            if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
                header("Location: /register?error=Invalid CSRF token");
                exit();
            }
            
            $fullName = $_POST['full-name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm-password'];

            // Check if the password is at least 8 characters long, contains a number, a special character, a small letter ,and a capital letter
            $errors = $this->validatePassword($password);
            if (!empty($errors)) {
                header("Location: /register?error=Password must have " . urlencode(implode(", ", $errors)));
                exit();
            }


            // Check if the passwords match
            if ($password != $confirmPassword) {
                header("Location: /register?error=Passwords do not match");
                exit();
            }

            // Register the user
            $user = $this->user->register($fullName, $email, $password);

            // Redirect to the home page if the user is registered successfully
            if ($user) {
                $_SESSION['user'] = $user;
                header("Location: /");
                exit();
            } else {
                header("Location: /register?error=Email already exists");
                exit();
            }
        }
    }

    public function deleteAccount() {
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            
            // Get the user ID from the session
            $user = $_SESSION['user'];
            $userId = $user['user_id'];

            // Validate CSRF token
            $headers = getallheaders();
            $csrf_token = $headers['X-CSRF-Token'] ?? '';
            if (!$csrf_token || $csrf_token !== $_SESSION['csrf_token']) {
                http_response_code(403);
                echo json_encode(['message' => 'Invalid CSRF token']);
                exit();
            }

            // Delete the user account
            $result = $this->user->deleteAccount($userId);

            // Redirect to the login page if the account was deleted successfully
            if ($result) {
                session_unset();
                session_destroy();
                header("Location: /login");
                exit();
            } else {
                header("Location: /userDetails?error=Delete account failed");
                exit();
            }
        }
    }

    public function makeAdmin() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            
            // Decode JSON payload
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
            
            // Check if userId is set in the decoded data
            if (isset($data['userId'])) {
                
                $userId = $data['userId'];
                                
                // Make the user an admin
                $result = $this->user->makeAdmin($userId);
    
                // Redirect to the user details page if the user was made an admin successfully
                if ($result) {
                    $_SESSION['user']['user_type'] = 'admin';
                    header("Location: /userDetails");
                    exit();
                } else {
                    header("Location: /userDetails?error=Make admin failed");
                    exit();
                }
            } else {
                // Handle the case where userId is not set in the request
                header("Location: /userDetails?error=User ID not provided");
                exit();
            }
        }
    }

    public function userAccess() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            // Decode JSON payload
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
            
            // Check if userId is set in the decoded data
            if (isset($data['userId'])) {
                
                $userId = $data['userId'];
                $userType = $data['userType'];
                $result = $this->user->userAccess($userId, $userType);
    
                if ($result) {
                    header("Location: /userDetails");
                    exit();
                } else {
                    header("Location: /userDetails?error=Block user failed");
                    exit();
                }
            } else {
                // Handle the case where userId is not set in the request
                header("Location: /userDetails?error=User ID not provided");
                exit();
            }
        }
    }

    public function deleteUser(){
        if ($_SERVER['REQUEST_METHOD'] == 'DELETE') {
            // Decode JSON payload
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
            
            // Check if userId is set in the decoded data
            if (isset($data['userId'])) {
                
                $userId = $data['userId'];
                $result = $this->user->deleteAccount($userId);
    
                if ($result) {
                    header("Location: /userDetails");
                    exit();
                } else {
                    header("Location: /userDetails?error=Delete user failed");
                    exit();
                }
            } else {
                // Handle the case where userId is not set in the request
                header("Location: /userDetails?error=User ID not provided");
                exit();
            }
        }
    
    }
    
}

$conn = require dirname(__DIR__) . '/db.php';
$controller = new UserController($conn, $config['DB_NAME']);

// Determine which function to call based on the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'login') !== false) {
    $controller->login();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'logout') !== false) {
    $controller->logout();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'register') !== false) {
    $controller->register();
} else if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && strpos($_SERVER['REQUEST_URI'], 'deleteAccount') !== false) {
    $controller->deleteAccount();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'makeAdmin') !== false) {
    $controller->makeAdmin();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'userAccess') !== false) {
    $controller->userAccess();
} else if($_SERVER['REQUEST_METHOD'] == 'DELETE' && strpos($_SERVER['REQUEST_URI'], 'deleteUser') !== false) {
    $controller->deleteUser();
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && strpos($_SERVER['REQUEST_URI'], 'login') !== false) {
    include dirname(__DIR__) . '/views/login.php';
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && strpos($_SERVER['REQUEST_URI'], 'register') !== false) {
    include dirname(__DIR__) . '/views/register.php';
} else if ($_SERVER['REQUEST_METHOD'] == 'GET' && strpos($_SERVER['REQUEST_URI'], 'userDetails') !== false) {
    include dirname(__DIR__) . '/views/userDetails.php';
}
?>
