<?php
session_start();
require_once dirname(__DIR__) . '/db.php';
require_once dirname(__DIR__) . '/models/User.php';

class UserController {
    private $user;

    public function __construct($db) {
        $this->user = new User($db);
    }

    public function login() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $email = $_POST['user_email'];
            $password = $_POST['user_password'];

            $user = $this->user->login($email, $password);

            if ($user) {
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
            session_start();
            session_unset();
            session_destroy();
            header("Location: /login");
            exit();
        }
    }

    public function register() {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $fullName = $_POST['full-name'];
            $email = $_POST['email'];
            $password = $_POST['password'];
            $confirmPassword = $_POST['confirm-password'];

            if ($password != $confirmPassword) {
                header("Location: /register?error=Passwords do not match");
                exit();
            }

            $user = $this->user->register($fullName, $email, $password);

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
}

$conn = require dirname(__DIR__) . '/db.php';
$controller = new UserController($conn);

// Determine which function to call based on the request
if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'login') !== false) {
    $controller->login();
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'logout') !== false) {
    $controller->logout();
} else if ($_SERVER['REQUEST_METHOD'] == 'POST' && strpos($_SERVER['REQUEST_URI'], 'register') !== false) {
    $controller->register();
}
?>
