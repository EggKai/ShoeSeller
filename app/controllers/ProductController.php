<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../models/Review.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';

class ProductController extends Controller
{
    private const PATH = 'product';
    public function detail($id)
    {
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        if ($product) {
            $sizes = $productModel->getSizesByShoeId($id);
            $category = $productModel->findById('categories', $product['category_id']);
            $this->view(ProductController::PATH . '/detail', [
                'product' => $product,
                'sizes' => $sizes,
                'category' => $category,
                'reviews' => (new Review())->getAllReviewsByProductId($id),
                'options' => ['addCart', 'review', 'detail'],
                'csrf_token' => Csrf::generateToken()
            ]);
        } else {
            $this->product();
        }
        exit;
    }

    public function product($query = null)
    {
        $productModel = new Product();
        if (!($query === null)) {
            $products = $productModel->searchProductByQuery($query);
            if ($products) {
                $this->view(ProductController::PATH . '/products', ['products' => $products, 'options' => ['floating-button']]);
                exit;
            }
            $this->view(ProductController::PATH . '/products', ['products' => [], 'options' => ['floating-button']]);
            exit;
        }
        if (!(isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee']))) {
            $products = $productModel->getAllListedProducts();
        } else {
            $products = $productModel->getAllProducts();
        }
        $this->view(ProductController::PATH . '/products', ['products' => $products, 'options' => ['floating-button']]);
        exit;
    }


    function plusCartItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $this->cart();
        }
        $productId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $this->cart(Cart::updateCartitemQuantity($productId, $size, 1));
        exit;
    }

    function minusCartItem()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $this->cart();
        }
        $productId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $this->cart(Cart::updateCartitemQuantity($productId, $size, -1));
        exit;
    }

    public function cart($cart = null, $alert = null)
    {
        if ($cart === null) { // cant pass parameters in runtime like py
            $cart = Cart::getCurrentCart();
        }
        $this->view(ProductController::PATH . '/cart', ['options' => ['cart', 'form'], 'cart' => Cart::fullCartDetails($cart), 'csrf_token' => Csrf::generateToken(), 'alert' => $alert]);
        exit;
    }
    public function createReview($productId)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // Check if user is logged in
        if (!isset($_SESSION['user'])) {
            die("Unauthorized");
        }

        // Validate CSRF token if needed
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) {
            die("Invalid request");
        }

        $rating = filter_input(INPUT_POST, 'rating', FILTER_SANITIZE_NUMBER_INT);
        $title = filter_var($_POST['title'], FILTER_SANITIZE_SPECIAL_CHARS);
        $text = filter_var($_POST['review_text'], FILTER_SANITIZE_SPECIAL_CHARS);
        $userId = $_SESSION['user']['id']; // get userid from current session

        // Insert the review into the database
        $reviewModel = new Review();
        $success = $reviewModel->createReview($productId, $userId, $rating, $title, $text);

        if ($success) {
            // Redirect back to product page
            $this->detail($productId);
            exit;
        } else {
            // Show error or redirect
            die("Failed to create review");
        }
    }
}
?>