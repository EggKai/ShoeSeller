<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/HomeController.php';

class AdminController extends Controller {
    private static $path = "admin";
    private static $productImages = __DIR__ . '/../../public/products/';
    public function addProduct() {
        if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin'){
            $this->view(AdminController::$path . '/addProduct', ['data' => null, 'options' => ['form', 'form-carousel'], 'csrf_token' => Csrf::generateToken()]);
        }
        exit;
    }
    public function doAddProduct() {
        $alert = function($message) { //lambda
            $this->view(self::$path . '/addProduct', [
                'data'       => $_POST,
                'alert'      => [$message, 2],
                'options'    => ['form', 'form-carousel'],
                'csrf_token' => Csrf::generateToken()
            ]);
        };
        // Ensure the request is POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            $alert("Invalid request. Please try again.");
            exit;
        }
        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $brand = filter_var($_POST['brand'], FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $fileName = str_replace(" ", "", $name);
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], AdminController::$productImages.$fileName.".jpg"); // Move the uploaded file to the desired folder
    }

}