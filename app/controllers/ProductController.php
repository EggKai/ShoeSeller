<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';

class ProductController extends Controller
{
    private static $path = 'product';
    public function detail($id)
    {
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        if ($product){
            $sizes = $productModel->getSizesByShoeId($id);
            $this->view(ProductController::$path . '/detail', ['product' => $product, 'sizes' => $sizes, 'options' => ['addCart', 'floating-button', 'sizes-list'], 'csrf_token' => Csrf::generateToken()]);
        } else{
            $this->product();
        }
        exit;
    }

    public function product($query=null)
    {
        $productModel = new Product();
        if (!($query === null)) {
            $products = $productModel->searchProductByQuery($query);
            if ($products) {
                $this->view(ProductController::$path . '/products', ['products' => $products]);
                exit;
            }
            $this->view(ProductController::$path . '/products', ['products' => []]);
            exit;
        } 
        $products = $productModel->getAllProducts();
        $this->view(ProductController::$path . '/products', ['products' => $products, 'options' => ['floating-button']]);
        exit;
    }


    function plusCartItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $this->cart();
        }
        $productId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $this->cart(Cart::updateCartitemQuantity($productId, $size,1));
        exit;
    }

    function minusCartItem() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST' && (filter_has_var(INPUT_POST, 'submit'))) {// Ensure the request is POST
            (new HomeController())->index();
            exit;
        }
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!Csrf::validateToken($csrfToken)) { // Validate CSRF token
            $this->cart();
        }
        $productId = filter_var($_POST['id'], FILTER_SANITIZE_NUMBER_INT);
        $size = filter_var($_POST['size'], FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
        $this->cart(Cart::updateCartitemQuantity($productId, $size,-1));
        exit;
    }

    public function cart($cart=null){
        if ($cart === null) { // cant pass parameters in runtime like py
            $cart = Cart::getCurrentCart();
        }
        $this->view(ProductController::$path . '/cart', ['options' => ['cart'], 'cart'=>Cart::fullCartDetails($cart), 'csrf_token' => Csrf::generateToken()]);
        exit;
    }
}
?>