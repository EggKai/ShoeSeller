<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/HomeController.php';

class AdminController extends Controller
{
    private static $path = "admin";
    private static $productImages = __DIR__ . '/../../public/products/';
    public function addProduct()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') {
            $categories = (new Product())->getAllCategories();
            $this->view(AdminController::$path . '/addProduct', ['data' => null, 'options' => ['form', 'form-carousel', 'sizes-list'], 'categories' => $categories, 'csrf_token' => Csrf::generateToken()]);
        }
        (new HomeController())->index();
        exit;
    }
    public function doAddProduct()
    {
        $alert = function ($message) { //lambda
            $categories = (new Product())->getAllCategories();
            $this->view(self::$path . '/addProduct', [
                'data' => $_POST,
                'alert' => [$message, 2],
                'categories' => $categories,
                'options' => ['form', 'form-carousel', 'sizes-list'],
                'csrf_token' => Csrf::generateToken()
            ]);
            exit;
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
        }
        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $brand = filter_var($_POST['brand'], FILTER_SANITIZE_SPECIAL_CHARS);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);
        // Process the sizes and stock values.
        // These are received as arrays from the dynamic form inputs.
        $sizes = $_POST['sizes'] ?? [];
        $stocks = $_POST['stock'] ?? [];
        echo $name.$price.$brand.$description;
        if (empty(trim($name)) || empty(trim($price)) || empty(trim($brand)) || empty(trim($description))) {
            $alert("All fields are required.");
        }
        if ($price <= 0) {
            $alert("Price cannot be below 0");
        }
        if (!$category) {
            // Handle error: no valid category was selected
            $alert("Invalid category selection.");
        }
        // Check that both arrays have the same length.
        if (count($sizes) !== count($stocks)) {
            $alert("Sizes and stock counts do not match.");
        }
        $fileName = str_replace(" ", "_", $name);

        $fileExtension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
        if (!array_key_exists($fileExtension, Auth::ALLOWED_EXTENTIONS)) {
            $alert("Invalid file type. Only JPG, JPEG, PNG, and AVIF images are allowed.");
        }
        if (Auth::ALLOWED_EXTENTIONS[$fileExtension] !== $_FILES['thumbnail']['type']) {
            $alert("Error: File MIME type does not match the expected type for .$fileExtension files.");
        }
        if (exif_imagetype($_FILES['thumbnail']['tmp_name']) === false) {
            $alert("Uploaded file is not a valid image.");
        }
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], AdminController::$productImages . $fileName . '.' . $fileExtension); // Move the uploaded file to the desired folder

        $productModel = new Product(); // Instantiate the Product model.

        // Insert product record. Assumes createProduct returns the new product's ID or false on failure.
        $productId = $productModel->createProduct($name, $brand, $price, $description, $fileName . '.' . $fileExtension, $category);
        foreach ($sizes as $index => $size) {
            // Sanitize each size value.
            $size = filter_var(trim($size), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_FRACTION);
            $stock = filter_var($stocks[$index], FILTER_SANITIZE_NUMBER_INT);
            // Skip any entries that are empty or invalid.
            if (empty($size) || !is_numeric($stock)) {
                continue;
            }
            // Insert each size with its stock into the product_sizes table.
            // Assumes addProductSize returns true on success or false on failure.
            $result = $productModel->addProductSize($productId, $size, $stock);
            if (!$result) {
                $alert('Server Failure! Try again later.');
            }

        }
        $categories = (new Product())->getAllCategories();
        $this->view(self::$path . '/addProduct', [
            'data' => null,
            'alert' => ["Add another product?", 1],
            'categories' => $categories,
            'options' => ['form', 'form-carousel', 'sizes-list'],
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }

}