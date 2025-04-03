<?php
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/../../core/log.php';
require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/ProductController.php';

class EmployeeController extends Controller
{
    public const PATH = "employee";
    private static $productImages = __DIR__ . '/../../public/products/';    
    public function __construct() {
        if (session_status() === PHP_SESSION_NONE) { // Start session if not already started.
            session_start();
        }
        if (!(isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee']))) { // Check if the user is an admin; if not, redirect.
            (new UserController())->login();
            exit;
        }
    }
    
    protected static function trackAction($action) {
        return logAction('ACTION Employee "'.($_SESSION['user']['name'] ?? 'unknown ').'" (id:'. ($_SESSION['user']['id'] ?? 'unknown') .') '. $action);
    }

    public function addProduct()
    {
        $categories = (new Product())->getAllCategories();
        $this->view(self::PATH . '/addProduct', [
            'data' => null,
            'options' => ['form', 'form-carousel', 'sizes-list'],
            'categories' => $categories,
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }

    public function updateProduct()
    {
        $productModel = new Product();
        $product = $productModel->getProductById(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $sizes = $productModel->getSizesByShoeId($product['id'] ?? 0);
        $categories = $productModel->getAllCategories();

        $this->view(self::PATH . '/updateProduct', [
            'product' => $product,
            'sizes' => $sizes,
            'categories' => $categories,
            'csrf_token' => Csrf::generateToken(),
            'options' => ['form', 'sizes-list', 'floating-button', 'form-carousel-forked']
        ]);
        exit;
    }

    public function doAddProduct()
    {
        $alert = function ($message) {
            $categories = (new Product())->getAllCategories();
            $this->view(self::PATH . '/addProduct', [
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
        // Validate CSRF token
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            $alert("Invalid request. Please try again.");
        }

        $name = htmlspecialchars(strip_tags($_POST['name']), ENT_COMPAT, 'UTF-8');
        $price = filter_var($_POST['price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $brand = filter_var($_POST['brand'], FILTER_SANITIZE_SPECIAL_CHARS);
        $description = htmlspecialchars(strip_tags($_POST['description']),  ENT_COMPAT, 'UTF-8');
        $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        // Process the sizes and stock values (arrays from the form).
        $sizes = $_POST['sizes'] ?? [];
        $stocks = $_POST['stock'] ?? [];

        if (empty(trim($name)) || empty(trim($price)) || empty(trim($brand)) || empty(trim($description))) {
            $alert("All fields are required.");
        }
        if ($price <= 0) {
            $alert("Price cannot be below 0");
        }
        if (!$category) {
            $alert("Invalid category selection.");
        }
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
        move_uploaded_file($_FILES['thumbnail']['tmp_name'], self::$productImages . $fileName . '.' . $fileExtension);

        $productModel = new Product();
        if ($productModel->productNameExists($name)){
            $alert("Another Shoe Exists with the same name");
        }
        // Insert product record. Assumes createProduct returns the new product's ID or false on failure.
        $productId = $productModel->createProduct(
            $name,
            $brand,
            $price,
            $description,
            $fileName . '.' . $fileExtension,
            $category
        );

        foreach ($sizes as $index => $size) {
            $size = filter_var(trim($size), FILTER_SANITIZE_NUMBER_INT, FILTER_FLAG_ALLOW_FRACTION);
            $stock = filter_var($stocks[$index], FILTER_SANITIZE_NUMBER_INT);
            if (empty($size) || !is_numeric($stock)) {
                continue;
            }
            $result = $productModel->addProductSize($productId, $size, $stock);
            if (!$result) {
                $alert('Server Failure! Try again later.');
            }
        }

        $categories = (new Product())->getAllCategories();
        self::trackAction('added product:'.$name.' Brand:'.$brand.' Price:$'.$price.' with asset '.$fileName . '.' . $fileExtension);
        $this->view(self::PATH . '/addProduct', [
            'data' => null,
            'alert' => ["Add another product?", 1],
            'categories' => $categories,
            'options' => ['form', 'form-carousel', 'sizes-list'],
            'csrf_token' => Csrf::generateToken()
        ]);
        exit;
    }

    public function doUpdateProduct()
    {
        $alert = function ($message, $status=2) {
            $productModel = new Product();
            $product = $productModel->getProductById(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
            $sizes = $productModel->getSizesByShoeId($product['id'] ?? 0);
            $categories = $productModel->getAllCategories();

            $this->view(self::PATH . '/updateProduct', [
                'alert' => [$message, $status],
                'product' => $product,
                'sizes' => $sizes,
                'categories' => $categories,
                'csrf_token' => Csrf::generateToken(),
                'options' => ['form', 'sizes-list', 'floating-button', 'form-carousel-forked']
            ]);
            exit;
        };
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !filter_has_var(INPUT_POST, 'submit')) {
            (new HomeController())->index();
            exit;
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            $alert("Invalid request. Please try again.");
        }

        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            $alert("Invalid product ID.");
        }

        $name = htmlspecialchars(strip_tags($_POST['name']),  ENT_COMPAT, 'UTF-8');
        $brand = filter_var($_POST['brand'], FILTER_SANITIZE_SPECIAL_CHARS);
        $price = filter_var($_POST['base_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = htmlspecialchars(strip_tags($_POST['description']),  ENT_COMPAT, 'UTF-8');


        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $fileExtension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));
            if (!array_key_exists($fileExtension, Auth::ALLOWED_EXTENTIONS)) {
                $alert("Invalid file type. Only JPG, JPEG, PNG, and AVIF images are allowed.");
            }
            if (Auth::ALLOWED_EXTENTIONS[$fileExtension] !== $_FILES['thumbnail']['type']) {
                $alert("File MIME type does not match the expected type for .$fileExtension files.");
            }
            if (getimagesize($_FILES['thumbnail']['tmp_name']) === false) {
                $alert("Uploaded file is not a valid image.");
            }
            $fileName = str_replace(" ", "_", $name);
            $destination = self::$productImages . $fileName . '.' . $fileExtension;
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $destination)) {
                $alert("Error uploading the thumbnail image.");
            }
            $thumbnail = $fileName . '.' . $fileExtension;
        }

        $productModel = new Product();
        $updated = $productModel->updateProduct($id, $name, $brand, $price, $description, $thumbnail);
        if (!$updated) {
            $alert("Error updating product in the database.");
        }
        self::trackAction('Updated details of product id:'.$id.'('.$name.')'.';');

        $sizesInput = $_POST['sizes'] ?? [];
        $stocksInput = $_POST['stock'] ?? [];

        if (count($sizesInput) !== count($stocksInput)) {
            $alert("Sizes and stock counts do not match.");
        }

        $submitted = [];
        foreach ($sizesInput as $index => $sizeValue) {
            $sizeValue = trim(filter_var($sizeValue, FILTER_SANITIZE_SPECIAL_CHARS));
            $stockValue = filter_var($stocksInput[$index], FILTER_SANITIZE_NUMBER_INT);
            if (!empty($sizeValue) && is_numeric($stockValue)) {
                $submitted[$sizeValue] = $stockValue;
            }
        }

        $currentSizesArr = $productModel->getSizesByShoeId($id);
        $current = [];
        foreach ($currentSizesArr as $record) {
            $current[$record['size']] = $record['stock'];
        }

        foreach ($submitted as $size => $stock) {
            if (array_key_exists($size, $current)) {
                if ($current[$size] != $stock) {
                    $productModel->updateProductSize($id, $size, $stock);
                    self::trackAction('Updated product id:'.$id.'('.$name.')'.'; Updated size:'.$size.', Stock:'.$stock);
                }
                unset($current[$size]);
            } else {
                $productModel->addProductSize($id, $size, $stock);
                self::trackAction('Updated product id:'.$id.'('.$name.')'.'; Added new size:'.$size.', Stock:'.$stock);
            }
        }

        foreach ($current as $size => $stock) { //delete sizes that werent given
            $productModel->deleteProductSize($id, $size);
            self::trackAction('Updated product id:'.$id.'('.$name.')'.'; Added new size:'.$size.', Stock:'.$stock);
        }

        $alert("Shoe Updated", 1);
        exit;
    }

    public function handleListing()
    {
        $alert = function ($message, $status=2) {
            $productModel = new Product();
            $product = $productModel->getProductById(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
            $sizes = $productModel->getSizesByShoeId($product['id'] ?? 0);
            $categories = $productModel->getAllCategories();

            $this->view(self::PATH . '/updateProduct', [
                'alert' => [$message, $status],
                'product' => $product,
                'sizes' => $sizes,
                'categories' => $categories,
                'csrf_token' => Csrf::generateToken(),
                'options' => ['form', 'sizes-list', 'floating-button', 'form-carousel-forked']
            ]);
            exit;
        };
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            $alert("Invalid product ID.");
        }

        $productModel = new Product();
        $updated = $productModel->handleListing($id);
        if (!$updated) {
            $alert("Error updating listing in the database.");
        }
        self::trackAction('updated listing status for product id:'.$id);

        $alert("Updated Listing", 1);
        exit;
    }
}
