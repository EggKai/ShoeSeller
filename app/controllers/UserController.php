<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../../core/log.php';
require_once __DIR__ . '/HomeController.php';

class UserController extends Controller {
    private const PATH = 'auth';

    public function login() {
        if (!isset($_SESSION['user'])) {
            $this->view(UserController::PATH . '/login', ['data' => null, 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
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
            $this->view(self::PATH . '/login', [
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
            logAction("INFO $email logged in");
            (new HomeController())->index();
            exit;
        } else {
            $alert("Invalid email or password.");
        }
    }
    public function register() {
        $this->view(UserController::PATH . '/register', ['data' => null, 'options' => ['form', 'form-carousel'], 'csrf_token' => Csrf::generateToken()]);
    }

    public function doRegister() {
        // Local lambda for rendering error messages.
        $alert = function($message) {
            $this->view(self::PATH . '/register', [
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
        if ($auth->emailExists($email)) {
            $alert("Email already exists");
        }
        $userId = $auth->register($name, $email, $password, $address, null, 'user');
        if ($userId) { // successfully created user
            logAction("INFO User $email($name) registered");
            $this->view(self::PATH . '/login', [
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
        $this->view(UserController::PATH . '/profile', ['user' => $_SESSION['user'], 'options' => ['profile'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function forgotPassword() {
        $this->view(UserController::PATH . '/forgotPassword', ['data' => null, 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function editProfile() {
        $this->view(UserController::PATH . '/editProfile', ['user' => $_SESSION['user'], 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function doEditProfile() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController)->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Validate CSRF token.
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            die("Invalid request.");
        }
        
        // Sanitize inputs.
        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $address = filter_var($_POST['address'], FILTER_SANITIZE_SPECIAL_CHARS);
        
        // Process profile picture upload if provided.
        $profilePic = $user['profile_pic'] ?? null; // keep current picture by default
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            // Validate image type as needed...
            $fileExtension = strtolower(pathinfo($_FILES['profile_pic']['name'], PATHINFO_EXTENSION));
            // Check allowed file types, e.g., with a constant array.
            if (!in_array($fileExtension, ['jpg', 'jpeg', 'png', 'avif'])) {
                $alert = ["Invalid image type.", 2];
                $this->view('auth/editProfile', ['user' => $_SESSION['user'], 'csrf_token' => Csrf::generateToken(), 'alert' => $alert]);
                exit;
            }
            // Optionally rename and move the file.
            $newFileName = str_replace(" ", "_", $name) . '.' . $fileExtension;
            move_uploaded_file($_FILES['profile_pic']['tmp_name'], __DIR__ . '/../../public/uploads/' . $newFileName);
            $profilePic = $newFileName;
        }
        $userModel = new Auth();
        $updated = $userModel->updateUser($_SESSION['user']['id'], $name, $email, $address, $profilePic);
        if ($updated) {
            $_SESSION['user'] = $userModel->getUserById($_SESSION['user']['id']);
            $alert = ["Profile updated successfully.", 1];
        } else {
            $alert = ["Failed to update profile.", 2];
        }
        $this->view('auth/editProfile', [
            'user' => $_SESSION['user'],
            'csrf_token' => Csrf::generateToken(),
            'alert' => $alert
        ]);
        exit;
    }
    

    public function resetPassword() {
        $alert = function($message, $status=2) {
            $this->view(self::PATH . '/forgotPassword', [
                'data'       => $_POST,
                'alert'      => [$message, $status],
                'options'    => ['form'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        };
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $alert("Invalid request. Please try again.");
        }
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $userModel = new Auth();
        if ($userModel->emailExists($email)) {
            $alert('You do not have an account with us! <a href="auth/register">Register Now</a>');
        }
    }
    public function reset() {
        $token = $_GET['token'] ?? '';
        $token = filter_var($token, FILTER_SANITIZE_STRING);
    
        $userModel = new Auth();
        $record = $userModel->getResetToken($token);
    
        if (!$record || $record['used'] == 1 || strtotime($record['expires_at']) < time()) {
            // Invalid or expired token
            $alert = ["Invalid or expired token.", 2];
            $this->view('auth/resetForm', [
                'alert' => $alert
            ]);
            exit;
        }
        $this->view('auth/resetForm', [ // Render the reset password form
            'token' => $token,
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }
    public function doReset() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController)->index();
            exit;
        }
        $csrfToken = $_POST['csrf_token'] ?? ''; // Validate CSRF token
        if (!Csrf::validateToken($csrfToken)) {
            die("Invalid request.");
        }
        $token = filter_var($_POST['token'], FILTER_SANITIZE_STRING);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        if ($password !== $confirmPassword) {
            $alert = ["Passwords do not match.", 2];
            $this->view('auth/resetForm', [
                'token' => $token,
                'alert' => $alert,
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        }
        $userModel = new Auth();
        $record = $userModel->getResetToken($token); // Check token
        if (!$record || $record['used'] == 1 || strtotime($record['expires_at']) < time()) {
            $alert = ["Invalid or expired token.", 2];
            $this->view('auth/resetForm', [
                'alert' => $alert
            ]);
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userModel->updatePassword($record['user_id'], $hashedPassword);
        $userModel->markTokenUsed($record['id']); // Mark token as used
        $alert = ["Password reset successfully.", 1];
        $this->view('auth/login', [
            'alert' => $alert
        ]);
        exit;
    }
    
}
?>