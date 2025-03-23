<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';

class ProductController extends Controller
{
    private const PATH = 'product';
    public function detail($id)
    {
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        if ($product){
            $sizes = $productModel->getSizesByShoeId($id);
            $this->view(ProductController::PATH . '/detail', ['product' => $product, 'sizes' => $sizes, 'options' => ['addCart']]);
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
                $this->view(ProductController::PATH . '/products', ['products' => $products]);
                exit;
            }
            $this->view(ProductController::PATH . '/products', ['products' => []]);
            exit;
        } 
        $products = $productModel->getAllProducts();
        $this->view(ProductController::PATH . '/products', ['products' => $products, 'options' => ['floating-button']]);
        exit;
    }


    function plusCartItem() {
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
        $this->cart(Cart::updateCartitemQuantity($productId, $size,1));
        exit;
    }

    function minusCartItem() {
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
        $this->cart(Cart::updateCartitemQuantity($productId, $size,-1));
        exit;
    }

    public function cart($cart=null, $alert=null){
        if ($cart === null) { // cant pass parameters in runtime like py
            $cart = Cart::getCurrentCart();
        }
        $this->view(ProductController::PATH . '/cart', ['options' => ['cart', 'form'], 'cart'=>Cart::fullCartDetails($cart), 'csrf_token' => Csrf::generateToken(), 'alert' => $alert]);
        exit;
    }
}
?>