<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Dashboard.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../../core/log.php';
require_once __DIR__ . '/HomeController.php';

class AdminController extends Controller
{
    public const PATH = "admin";

    public function __construct()
    {
        if (session_status() === PHP_SESSION_NONE) { // Start session if not already started.
            session_start();
        }
        if (!isset($_SESSION['user']) || $_SESSION['user']['user_type'] !== 'admin') { // Check if the user is an admin; if not, redirect.
            if (isset($_SESSION['user'])) {
                logAction("ALERT ".ucfirst($_SESSION['user']['user_type']).' "'.$_SESSION['user']['name'].'" id:'.$_SESSION['user']['id'].' tried to access admin route; IP Address['.getIP().']');
            } else {
                logAction("ALERT guest tried to access admin route; IP Address[".getIP().']');
            }
            (new HomeController())->index();
            exit;
        }
    }

    protected static function trackAction($action) {
        return logAction('ACTION Admin '.($_SESSION['user']['name'] ?? 'unknown'). '(id:'. ($_SESSION['user']['id'] ?? 'unknown') .') '. $action);
    }

    public function viewUsers()
    {
        $userModel = new Auth();
        $users = $userModel->getAllUsers();

        $this->view(self::PATH . '/users', [
            'users' => $users,
            'options' => ['view-users', 'floating-button'],
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }

    public function createUser()
    {
        $this->view(self::PATH . '/createUser', [
            'options' => ['form'],
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }
    public function dashboard()
    {
        $dashboardModel = new Dashboard();
        $data = $dashboardModel->getDashboardData();

        // Process revenue per day for Chart.js
        $revenuePerDayLabels = [];
        $revenuePerDayData = [];
        foreach ($data['revenuePerDay'] as $row) {
            $revenuePerDayLabels[] = $row['day'];
            $revenuePerDayData[] = (float) $row['revenue'];
        }

        // Process shoes by category for Pie Chart
        $shoesByCategoryLabels = [];
        $shoesByCategoryData = [];
        foreach ($data['shoesByCategory'] as $row) {
            $shoesByCategoryLabels[] = $row['category'];
            $shoesByCategoryData[] = (int) $row['count'];
        }

        // Process shoes sold by category for Bar Chart
        $shoesSoldByCategoryLabels = [];
        $shoesSoldByCategoryData = [];
        foreach ($data['shoesSoldByCategory'] as $row) {
            $shoesSoldByCategoryLabels[] = $row['category'];
            $shoesSoldByCategoryData[] = (int) $row['shoes_sold'];
        }

        // Process reviews per day for Chart.js
        $reviewsPerDayLabels = [];
        $reviewsPerDayData = [];
        foreach ($data['reviewsPerDay'] as $row) {
            $reviewsPerDayLabels[] = $row['day'];
            $reviewsPerDayData[] = (int) $row['reviews'];
        }

        // Pass data to the view.
        $this->view('admin/dashboard', [
            'totalRevenue' => $data['totalRevenue'],
            'totalShoesSold' => $data['totalShoesSold'],
            'totalSales' => $data['totalSales'],
            'totalUsers' => $data['totalUsers'],
            'revenuePerDayLabels' => $revenuePerDayLabels,
            'revenuePerDayData' => $revenuePerDayData,
            'shoesByCategoryLabels' => $shoesByCategoryLabels,
            'shoesByCategoryData' => $shoesByCategoryData,
            'shoesSoldByCategoryLabels' => $shoesSoldByCategoryLabels,
            'shoesSoldByCategoryData' => $shoesSoldByCategoryData,
            'reviewsPerDayLabels' => $reviewsPerDayLabels,
            'reviewsPerDayData' => $reviewsPerDayData,
            'options' => ['dashboard']
        ]);
        exit;
    }

    public function deleteUser()
    { //ajax handling
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') { // Ensure the request is POST
            (new HomeController)->index();
            exit;
        }
        header('Content-Type: application/json');
        // Get input data
        $input = json_decode(file_get_contents('php://input'), true);
        $csrfToken = $input['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            echo json_encode(['success' => false, 'message' => 'Invalid CSRF token.']);
            exit;
        }

        $userId = filter_var($input['user_id'], FILTER_SANITIZE_NUMBER_INT);
        $adminPassword = $input['admin_password'] ?? '';

        // Validate the admin's password by fetching the current admin's details
        $userModel = new Auth();
        $admin = $userModel->getUserById($_SESSION['user']['id']);
        if (!$admin || !password_verify($adminPassword, $admin['password'])) {
            echo json_encode(['success' => false, 'message' => 'Incorrect admin password.']);
            exit;
        }

        // Proceed to delete the user (ensure admin can't delete themselves, if desired)
        if ($userId == $_SESSION['user']['id']) {
            echo json_encode(['success' => false, 'message' => 'You cannot delete your own account.']);
            exit;
        }

        if ($userModel->deleteUser($userId)) {
            self::trackAction("Deleted user with id:".$userId);
            echo json_encode(['success' => true]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to delete user.']);
        }
        exit;
    }

    public function doCreateUser()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController)->index();
            exit;
        }
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            die("Invalid request.");
        }
        // Retrieve and sanitize input values.
        $name = htmlspecialchars(strip_tags($_POST['name']), ENT_COMPAT, 'UTF-8');
        $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
        $password = $_POST['password'] ?? '';
        $confirmPassword = $_POST['confirm_password'] ?? '';
        $adminPassword = $_POST['admin_password'] ?? '';
        $userType = filter_input(INPUT_POST, 'user_type', FILTER_SANITIZE_SPECIAL_CHARS);

        // Check that all fields are provided.
        if (empty($name) || empty($email) || empty($password) || empty($confirmPassword) || empty($adminPassword) || empty($userType)) {
            $alert = ["All fields are required.", 2];
            $this->view(self::PATH . '/createUser', [
                'csrf_token' => Csrf::generateToken(),
                'data' => $_POST,
                'alert' => $alert,
            'options' => ['form'],
            ]);
            exit;
        }

        // Check that password and confirm password match.
        if ($password !== $confirmPassword) {
            $alert = ["Passwords do not match.", 2];
            $this->view(self::PATH . '/createUser', [
                'csrf_token' => Csrf::generateToken(),
                'data' => $_POST,
                'alert' => $alert,
                'options'=>['form']
            ]);
            exit;
        }
        

        $userModel = new Auth();
        $admin = $userModel->getUserById($_SESSION['user']['id']);
        if (!$admin || !password_verify($adminPassword, $admin['password'])) {
            $alert = ["Incorrect admin password.", 2];
            $this->view(self::PATH . '/createUser', [
                'csrf_token' => Csrf::generateToken(),
                'data' => $_POST,
                'alert' => $alert,
                'options'=>['form']

            ]);
            exit;
        }

        // Check if the email is already in use.
        if ($userModel->emailExists($email)) {
            $alert = ["The email address is already registered.", 2];
            $this->view(self::PATH . '/createUser', [
                'csrf_token' => Csrf::generateToken(),
                'data'       => $_POST,
                'alert'      => $alert,
                'options'=>['form']

            ]);
            exit;
        }
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Create the user (assuming your User model has a createUser method)
        if ($userModel->register($name, $email, $hashedPassword, null, null, $userType)) {
            self::trackAction("Created " .$userType." ".$name.'('.$email.')');
            $alert = ["User created successfully.", 1];
        } else {
            $alert = ["Failed to create user.", 2];
        }

        $this->view(self::PATH . '/createUser', [
            'csrf_token' => Csrf::generateToken(),
            'alert' => $alert,
            'data'       => $_POST,
            'options' => ['form'],
        ]);
        exit;
    }

    public function viewLogs()
{
    $allFiles = scandir(LOG_DIR); // Scan the directory for log files
    $logFiles = array_filter($allFiles, function($file) { // Filter out '.' and '..' plus any non-log files
        return !in_array($file, ['.', '..']) && str_ends_with($file, '.log');
    });

    // Prepare variables to pass to the view.
    $selectedFile = null;
    $fileContents = null;

    // Check if a specific file is requested via GET param 'file'.
    if (isset($_GET['file'])) {
        $requestedFile = basename($_GET['file']); // sanitize the file name
        if (in_array($requestedFile, $logFiles)) {
            // Safely read the file
            $selectedFile = $requestedFile;
            $fullPath = LOG_DIR . '/' . $requestedFile;
            $fileContents = @file_get_contents($fullPath);
            // Alternatively, handle errors if file_get_contents fails.
        } else {
            // If file is not in the log directory list, handle error or ignore.
            $fileContents = "Invalid file selection.";
        }
    }

    // Render the view, passing the list of files and (optionally) contents.
    $this->view( self::PATH.'/viewLogs', [
        'logFiles' => $logFiles,
        'selectedFile' => $selectedFile,
        'fileContents' => $fileContents,
        'options' => ['view-logs'] // for dynamic CSS/JS if needed
    ]);
    exit;
}
}