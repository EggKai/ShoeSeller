<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/RememberToken.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../../core/log.php';
require_once __DIR__ . '/HomeController.php';

class UserController extends Controller
{
    private const PATH = 'auth';

    public function login($alert = null)
    {
        if (!isset($_SESSION['user'])) {
            $this->view(UserController::PATH . '/login', ['data' => null, 'alert' => $alert, 'options' => ['form', 'captcha'], 'csrf_token' => Csrf::generateToken()]);
            exit;
        }
        (new HomeController)->index();
        exit;
    }

    public function logout()
    {
        if (session_status() === PHP_SESSION_NONE) {
            //session_status() returns three constants:
            //PHP_SESSION_DISABLED if sessions are disabled.
            //PHP_SESSION_NONE if sessions are enabled, but none exists.
            //PHP_SESSION_ACTIVE if sessions are enabled, and one exists.
            session_start();
        }

        // If a user is logged in, clear any remember-me tokens from the database.
        if (isset($_SESSION['user'])) {
            $rememberTokenModel = new RememberToken();
            // This method should delete all tokens associated with the user.
            $rememberTokenModel->deleteTokensByUser($_SESSION['user']['id']);
        }

        // Clear the remember_me cookie.
        if (isset($_COOKIE['remember_me'])) {
            setcookie('remember_me', '', time() - 3600, '/', '', true, true);
        }

        $_SESSION = array(); //set the session array to blank

        // If it's desired to kill the session, also delete the session cookie.
        // Note: This will destroy the session, and not just the session data!
        if (ini_get("session.use_cookies")) {
            $cookieInfo = session_get_cookie_params();
            //Returns an array with the current session cookie information.
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $cookieInfo["path"],
                $cookieInfo["domain"],
                $cookieInfo["secure"],
                $cookieInfo["httponly"]
            );
        }
        session_destroy();
        (new HomeController())->index();
        exit;
    }
    public function doLogin()
    {   
        
        $alert = function ($message) { //lambda
            $this->view(self::PATH . '/login', [
                'data' => $_POST,
                'alert' => [$message, 2],
                'options' => ['form', 'captcha'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        };
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            $alert("Captcha Incomplete. Please Try again.");
        }
        $responseData = google_recaptcha($recaptchaResponse);
        if (!$responseData['success']) {
            $alert("Captcha verification failed. Please try again.");
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
            $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
            $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];
            logAction("INFO $email logged in");
            if (isset($_POST['remember'])) {
                $rememberModel = new RememberToken();

                // Generate a secure random token (64 hex characters = 32 bytes)
                $token = generate_token();
                $tokenHash = password_hash($token, PASSWORD_DEFAULT);
                $expiresAt = date("Y-m-d H:i:s", time() + (30 * 24 * 60 * 60)); // 30 days

                $rememberModel->storeToken($user['id'], $tokenHash, $expiresAt);

                // Save both user ID and the raw token in a JSON-encoded cookie.
                $cookieData = json_encode([
                    'user_id' => $user['id'],
                    'token' => $token
                ]);
                setcookie(
                    'remember_me',
                    $cookieData,
                    time() + (30 * 24 * 60 * 60),
                    '/',
                    '',
                    true,  // Secure flag: ensure you're using HTTPS
                    true   // HttpOnly flag
                );
            }
            (new HomeController())->index();
            exit;
        } else {
            $alert("Invalid email or password.");
        }
    }
    public function register()
    {
        $this->view(UserController::PATH . '/register', ['data' => null, 'options' => ['form', 'form-carousel', 'captcha'], 'csrf_token' => Csrf::generateToken()]);
    }

    public function doRegister()
    {
        // Local lambda for rendering error messages.
        $alert = function ($message) {
            $this->view(self::PATH . '/register', [
                'data' => $_POST,
                'alert' => [$message, 2],
                'options' => ['form', 'form-carousel','captcha'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        };
        // Ensure the request is a POST and that the form was submitted.
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        }
        $recaptchaResponse = $_POST['g-recaptcha-response'] ?? '';
        if (empty($recaptchaResponse)) {
            $alert("Captcha incomplete. Please Try again.");
        }
        $responseData = google_recaptcha($recaptchaResponse);
        if (!$responseData['success']) {
            $alert("Captcha verification failed. Please try again.");
        }
        // Validate CSRF token.
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            $alert("Invalid request. Please try again.");
        }
        // Sanitize and validate input data.
        $name = htmlspecialchars(strip_tags($_POST['name']), ENT_COMPAT, 'UTF-8');
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $password = trim(strip_tags($_POST['password'] ?? ''));
        $cnfmPassword = trim(strip_tags($_POST['cnfmpassword'] ?? ''));
        $address = filter_input(INPUT_POST, 'address', FILTER_SANITIZE_SPECIAL_CHARS);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { // Validate email
            $alert("Invalid email address.");
        }
        if (empty($name)) {
            $alert("Name is required.");
        }
        if (strlen($password) < 6) {
            $alert("Password must be at least 6 characters long.");
        }
        if (!(preg_match('/[A-Z]/', $password) && preg_match('/[a-z]/', $password) && preg_match('/\d/', $password))) {
            $alert("Password does not meet complexity standards");
        }
        if (!($password === $cnfmPassword)) { // check passwords match
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
                'data' => $_POST,
                'alert' => ["You have made an account<3 Login now!", 1],
                'options' => ['form', 'captcha'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        } else {
            $alert("Registration failed. Please try again.");
        }
    }
    public function profile()
    {   
        (new Auth)->refreshUser();
        $this->view(UserController::PATH . '/profile', ['user' => $_SESSION['user'], 'options' => ['profile'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function forgotPassword()
    {
        $this->view(UserController::PATH . '/forgotPassword', ['data' => null, 'options' => ['form'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function editProfile()
    {
        $this->view(UserController::PATH . '/editProfile', ['user' => $_SESSION['user'], 'options' => ['form', 'profile'], 'csrf_token' => Csrf::generateToken()]);
    }
    public function doEditProfile()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController)->index();
            exit;
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
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->view(self::PATH .'/editProfile', ['user' => $_SESSION['user'], 'options' => ['form', 'profile'], 'csrf_token' => Csrf::generateToken(), 'alert' => ["Email not valid", 2]]);
            exit;
        }
        // Process profile picture upload if provided.
        $profilePic = $user['profile_pic'] ?? null; // keep current picture by default
        if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === 0) {
            if (!file_exists($_FILES['profile_pic']['tmp_name']) || !is_readable($_FILES['profile_pic']['tmp_name'])) {
                $alert = ["Uploaded file is not a valid image.", 2];
                $this->view(self::PATH .'/editProfile', ['user' => $_SESSION['user'], 'csrf_token' => Csrf::generateToken(), 'alert' => $alert, 'options' => ['form', 'profile']]);
            }
            if (exif_imagetype($_FILES['profile_pic']['tmp_name']) === false) {
                $alert = ["Uploaded file is not a valid image.", 2];
                $this->view(self::PATH .'/editProfile', ['user' => $_SESSION['user'], 'csrf_token' => Csrf::generateToken(), 'alert' => $alert, 'options' => ['form', 'profile']]);
            }
            try {
                $profilePic = convertImageToJpeg($_FILES['profile_pic']['tmp_name'], 90);
            } catch (Exception $e) {
                $alert = [$e->getMessage(), 2];
                $this->view(self::PATH .'/editProfile', ['user' => $_SESSION['user'], 'csrf_token' => Csrf::generateToken(), 'alert' => $alert, 'options' => ['form', 'profile',]]);
            }
        } else {
            $profilePic = null;
        }
        $userModel = new Auth();
        $updated = $userModel->updateUser($_SESSION['user']['id'], $name, $email, $address, $profilePic);
        if ($updated) {
            $_SESSION['user'] = $userModel->getUserById($_SESSION['user']['id']);
            $alert = ["Profile updated successfully.", 1];
        } else {
            $alert = ["Failed to update profile.", 2];
        }
        $userModel->refreshUser();
        $this->view(self::PATH .'/editProfile', [
            'user' => $_SESSION['user'],
            'csrf_token' => Csrf::generateToken(),
            'alert' => $alert,
            'options' => ['form', 'profile']
        ]);

        exit;
    }


    public function resetPassword()
    {
        $alert = function ($message, $status = 2) {
            $this->view(self::PATH . '/forgotPassword', [
                'data' => $_POST,
                'alert' => [$message, $status],
                'options' => ['form'],
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
        if (!$userModel->emailExists($email)) {
            $alert('You do not have an account with us! <a href="/auth/register">Register Now</a>');
        }
        $user = $userModel->getUserByEmail($email);
        $userId = $user['id'];
        $token = generate_token();
        if (!sendPasswordResetEmail($email, $user['name'], $token)) {
            $alert('Unable to send email, Please try again later');
        }
        $userModel->createResetToken($userId, $token, date('Y-m-d H:i:s', strtotime('+1 hour')));
        logAction('INFO ' . $user['user_type'] . ' ' . $user['name'] . "(id:$userId) has requested to change their password");
        $alert('Email Sent, expires in one hour!', 1);

    }
    public function reset()
    {
        $token = $_GET['token'] ?? '';
        $token = filter_var($token, FILTER_SANITIZE_STRING);

        $userModel = new Auth();
        $record = $userModel->getResetToken($token);

        if (!$record || ($record['used'] == 1 || strtotime($record['expires_at']) < time())) {
            // Invalid or expired token
            $alert = ["Invalid or expired token.", 2];
            $this->view(self::PATH .'/resetForm', [
                'alert' => $alert,
                'options' => ['form']
            ]);
            exit;
        }
        $this->view(self::PATH .'/resetForm', [ // Render the reset password form
            'token' => $token,
            'csrf_token' => Csrf::generateToken(),
            'options' => ['form']
        ]);
        exit;
    }
    public function doReset()
    {
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
            $this->view(self::PATH .'/resetForm', [
                'token' => $token,
                'alert' => $alert,
                'options' => ['form'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
        }
        $userModel = new Auth();
        $record = $userModel->getResetToken($token); // Check token
        if (!$record || $record['used'] == 1 || strtotime($record['expires_at']) < time()) {
            $alert = ["Invalid or expired token.", 2];
            $this->view(self::PATH .'/resetForm', [
                'alert' => $alert
            ]);
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $userModel->updatePassword($record['user_id'], $hashedPassword);
        $userModel->markTokenUsed($record['id']); // Mark token as used
        $alert = ["Password reset successfully.", 1];
        logAction('INFO user id:' . $record['user_id'] . ' successfully changed their password');
        $this->login($alert);
        exit;
    }
    public function orderHistory() {
        // Retrieve orders for the logged-in user.
        $userId = $_SESSION['user']['id'];
        $orderModel = new Order();
        $orders = $orderModel->getOrdersByUserId($userId);
        // $orders = [];
        
        // Pass orders to the view.
        $this->view(self::PATH .'/orderHistory', ['orders' => $orders, 'options'=>['orderHistory']]);
        exit;
    }
}
?>