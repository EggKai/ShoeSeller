<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/HomeController.php';

class UserController extends Controller {
    private static $path = 'auth';

    public function login() {
        if (!isset($_SESSION['user'])) {
            $this->view(UserController::$path . '/login', ['data' => null, 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
            exit;
        }
        (new HomeController)->index();
        exit;
    }

    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            //session_status() returns three constants:
                //PHP_SESSION_DISABLED if sessions are disabled.
                //PHP_SESSION_NONE if sessions are enabled, but none exists.
                //PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
            session_start();
        }
        $_SESSION = array(); //set the session array to blank
        
        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $cookieInfo = session_get_cookie_params();
            //Returns an array with the current session cookie information.
            setcookie(session_name(), '', time() - 42000,
                $cookieInfo["path"], $cookieInfo["domain"], $cookieInfo["secure"], $cookieInfo["httponly"]
            );
        }
        // Finally, destroy the session.
        session_destroy();
        (new HomeController())->index();
        exit;
    }
    
    public function doLogin() {
        $alert = function($message) { //lambda
            $this->view(self::$path . '/login', [
                'data'       => $_POST,
                'alert'      => [$message, 2],
                'options'    => ['form'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        };
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $alert("Invalid request. Please try again.");
        }
        // Sanitize and validate input data
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = trim(strip_tags($_POST['password'] ?? ''));
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $alert("Invalid email address.");
        }
        $auth = new Auth(); // Authenticate using the Auth model
        $user = $auth->authenticate($email, $password);
        if ($user) {
            // Regenerate session ID to prevent session fixation
            session_regenerate_id(true);
            $_SESSION['user'] = $user;
            (new HomeController())->index();
            exit;
        } else {
            $alert("Invalid email or password.");
        }
    }
    public function register() {
        $this->view(UserController::$path . '/register', ['data' => null, 'options' => ['form', 'form-carousel'], 'csrf_token' => Csrf::generateToken()]);
    }

    public function doRegister() {
        // Local lambda for rendering error messages.
        $alert = function($message) {
            $this->view(self::$path . '/register', [
                'data'       => $_POST,
                'alert'      => [$message, 2],
                'options'    => ['form', 'form-carousel'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        };
        // Ensure the request is a POST and that the form was submitted.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        } 
        // Start the session if it's not already started.
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Validate CSRF token.
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            $alert("Invalid request. Please try again.");
        }
        // Sanitize and validate input data.
        $name     = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_SPECIAL_CHARS);
        $email    = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = trim(strip_tags($_POST['password'] ?? ''));
        $cnfmPassword = trim(strip_tags($_POST['cnfmpassword'] ?? ''));
        $address  = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email
            $alert("Invalid email address.");
        }
        if (empty($name)) {
            $alert("Name is required.");
        }
        if (strlen($password) < 6) {
            $alert("Password must be at least 6 characters long.");
        }
        if (!(preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/\d/', $password))){
            $alert("Password does not meet complexity standards");
        }
        if (!($password === $cnfmPassword)){ // check passwords match
            $alert("Password does do not match");
        }
        $auth = new Auth(); // Create an instance of the Auth model and attempt to register the user.
        $userId = $auth->register($name, $email, $password, $address, null, 'user');
        if ($userId) { // successfully created user
            $this->view(self::$path . '/login', [
                'data'       => $_POST,
                'alert'      => ["You have made an account<3 Login now!", 1],
                'options'    => ['form'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        } else {
            $alert("Registration failed. Please try again.");
        }
    }
    public function profile() {
        $this->view(UserController::$path . '/profile', ['user' => $_SESSION['user'], 'options' => ['profile'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function forgotPassword() {
        $this->view(UserController::$path . '/forgotPassword', ['data' => null, 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
    }
}
?>