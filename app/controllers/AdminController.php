<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../models/Auth.php';
require_once __DIR__ . '/../../core/Security.php';
require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/ProductController.php';

class AdminController extends Controller
{
    private static $path = "admin";
    private static $productImages = __DIR__ . '/../../public/products/';
    public function addProduct()
    {
        if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') {
            $categories = (new Product())->getAllCategories();
            $this->view(AdminController::$path . '/addProduct', ['data' => null, 'options' => ['form', 'form-carousel', 'sizes-list'], 'categories' => $categories, 'csrf_token' => Csrf::generateToken()]);
            exit;
        }
        (new HomeController())->index();
        exit;
    }
    public function updateProduct()
    {
        $productModel = new Product();
        $product = $productModel->getProductById(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
        $sizes = $productModel->getSizesByShoeId($product['id'] ?? 0);
        $categories = $productModel->getAllCategories();

        $this->view(self::$path . '/updateProduct', [
            'data' => null,
            'product' => $product,
            'sizes' => $sizes,
            'categories' => $categories,
            'csrf_token' => Csrf::generateToken(),
            'options' => ['form', 'form-carousel', 'sizes-list',  'floating-button']
        ]);
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
    public function doUpdateProduct()
    {
        // Lambda for handling errors and re-rendering the edit form
        $alert = function ($message) {
            // Retrieve current product and sizes for re-rendering
            $productModel = new Product();
            $product = $productModel->getProductById(filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT));
            $sizes = $productModel->getSizesByShoeId($product['id'] ?? 0);
            $categories = $productModel->getAllCategories(); // if needed

            $this->view(self::$path . '/updateProduct', [
                'data' => $_POST,
                'alert' => [$message, 2],
                'product' => $product,
                'sizes' => $sizes,
                'categories' => $categories,
                'csrf_token' => Csrf::generateToken(),
                'options' => ['form', 'form-carousel', 'sizes-list', 'floating-button']
            ]);
            exit;
        };

        // Ensure request is POST and a submit button is present
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !filter_has_var(INPUT_POST, 'submit')) {
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

        // Get and sanitize inputs
        $id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
        if (!$id) {
            $alert("Invalid product ID.");
        }
        $name = filter_var($_POST['name'], FILTER_SANITIZE_SPECIAL_CHARS);
        $brand = filter_var($_POST['brand'], FILTER_SANITIZE_SPECIAL_CHARS);
        $price = filter_var($_POST['base_price'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $description = filter_var($_POST['description'], FILTER_SANITIZE_SPECIAL_CHARS);

        // Optional: handle category update if your form includes a dropdown.
        // $category = filter_input(INPUT_POST, 'category', FILTER_SANITIZE_NUMBER_INT);

        // Process the thumbnail if a new file is provided.
        $thumbnail = null;
        if (isset($_FILES['thumbnail']) && $_FILES['thumbnail']['error'] === 0) {
            $fileExtension = strtolower(pathinfo($_FILES['thumbnail']['name'], PATHINFO_EXTENSION));

            // Check allowed file extensions using a predefined constant from Auth (adjust as needed).
            if (!array_key_exists($fileExtension, Auth::ALLOWED_EXTENTIONS)) {
                $alert("Invalid file type. Only JPG, JPEG, PNG, and AVIF images are allowed.");
            }
            if (Auth::ALLOWED_EXTENTIONS[$fileExtension] !== $_FILES['thumbnail']['type']) {
                $alert("File MIME type does not match the expected type for .$fileExtension files.");
            }
            if (getimagesize($_FILES['thumbnail']['tmp_name']) === false) {
                $alert("Uploaded file is not a valid image.");
            }
            // Create a filename using product name (replace spaces) and append the extension.
            $fileName = str_replace(" ", "_", $name);
            $destination = AdminController::$productImages . $fileName . '.' . $fileExtension;
            if (!move_uploaded_file($_FILES['thumbnail']['tmp_name'], $destination)) {
                $alert("Error uploading the thumbnail image.");
            }
            $thumbnail = $fileName . '.' . $fileExtension;
        }

        // Instantiate Product model and update product record.
        $productModel = new Product();
        // Update product record. (Assumes updateProduct returns true on success.)
        $updated = $productModel->updateProduct($id, $name, $brand, $price, $description, $thumbnail);
        if (!$updated) {
            $alert("Error updating product in the database.");
        }

        // Process sizes and stock arrays.
        $sizesInput = $_POST['sizes'] ?? [];
        $stocksInput = $_POST['stock'] ?? [];

        if (count($sizesInput) !== count($stocksInput)) {
            $alert("Sizes and stock counts do not match.");
            exit;
        }

        // Build an associative array for submitted sizes: [ size => stock ]
        $submitted = [];
        foreach ($sizesInput as $index => $sizeValue) {
            $sizeValue = trim(filter_var($sizeValue, FILTER_SANITIZE_SPECIAL_CHARS));
            $stockValue = filter_var($stocksInput[$index], FILTER_SANITIZE_NUMBER_INT);
            if (!empty($sizeValue) && is_numeric($stockValue)) {
                $submitted[$sizeValue] = $stockValue;
            }
        }

        // Retrieve current sizes from the database for this product.
        $currentSizesArr = $productModel->getSizesByShoeId($id); // Returns array of arrays with keys 'size' and 'stock'
        $current = [];
        foreach ($currentSizesArr as $record) {
            $current[$record['size']] = $record['stock'];
        }

        // Loop through submitted sizes.
        foreach ($submitted as $size => $stock) {
            if (array_key_exists($size, $current)) {
                // If size exists, update only if stock has changed.
                if ($current[$size] != $stock) {
                    $productModel->updateProductSize($id, $size, $stock);
                }
                // Remove this size from the $current array so that remaining sizes are ones to be deleted.
                unset($current[$size]);
            } else {
                // Insert the new size. (Using product's base price here; adjust if needed.)
                $productModel->addProductSize($id, $size, $stock);
            }
        }

        // Delete any sizes that remain in the $current array (they weren't submitted).
        foreach ($current as $size => $stock) {
            $productModel->deleteProductSize($id, $size);
        }
        (new ProductController)->detail($id);
        exit;
    }
}