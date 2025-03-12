<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Auth.php';
require_once __DIR__ . '/HomeController.php';

class AdminController extends Controller
{
    private static $path = "admin";
    private static $productImages = __DIR__ . '/../../public/products/';
    public function addProduct()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') {
            $this->view(AdminController::$path . '/addProduct', ['data' => null, 'options' => ['form', 'form-carousel', 'sizes-list'], 'csrf_token' => Csrf::generateToken()]);
        }
        exit;
    }
    public function doAddProduct()
    {
        $alert = function ($message) { //lambda
            $this->view(self::$path . '/addProduct', [
                'data' => $_POST,
                'alert' => [$message, 2],
                'options' => ['form', 'form-carousel', 'sizes-list'],
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
        // Process the sizes and stock values.
        // These are received as arrays from the dynamic form inputs.
        $sizes = $_POST['sizes'] ?? [];
        $stocks = $_POST['stock'] ?? [];

        // Check that both arrays have the same length.
        if (count($sizes) !== count($stocks)) {
            $alert("Sizes and stock counts do not match.");
            exit;
        }
        $fileName = str_replace(" ", "_", $name);

        $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
        if (!in_array($fileExtension, Auth::ALLOWED_EXTENTIONS)) {
            $alert("Invalid file type. Only JPG, JPEG, PNG, and AVIF images are allowed.");
            exit;
        }
        $imageInfo = getimagesize($_FILES['thumbnail']['tmp_name']);
        if ($imageInfo === false) {
            $alert("Uploaded file is not a valid image.");
            exit;
        }
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], AdminController::$productImages . $fileName . $fileExtension); // Move the uploaded file to the desired folder
        
        $productModel = new Product(); // Instantiate the Product model.

        // Insert product record. Assumes createProduct returns the new product's ID or false on failure.
        $productId = $productModel->createProduct($name, $brand, $price, $description, $fileName . $fileExtension);
        foreach ($sizes as $index => $size) {
            // Sanitize each size value.
            $size = filter_var(trim($size), FILTER_SANITIZE_SPECIAL_CHARS);
            $stock = filter_var($stocks[$index], FILTER_SANITIZE_NUMBER_INT);

            // Skip any entries that are empty or invalid.
            if (empty($size) || !is_numeric($stock)) {
                continue;
            }

            // Insert each size with its stock into the product_sizes table.
            // Assumes addProductSize returns true on success or false on failure.
            $result = $productModel->addProductSize($productId, $size, $stock);
            if (!$result) {
                // Optionally handle the error per size insertion (log, alert, etc.)
                // Here we simply continue.
                continue;
            }
        }
    }

}