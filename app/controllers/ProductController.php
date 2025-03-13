<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';
require_once __DIR__ . '/../../core/Security.php';

class Cart {
    public static function getCurrentCart() {
        $cart = [];
        if (isset($_COOKIE['cart'])) {
            $decoded = json_decode(urldecode($_COOKIE['cart']), true);
            if (is_array($decoded)) {
                $cart = $decoded;
            }
        }
        return $cart;
    }
    /**
     * Update the cart cookie with the provided cart array.
     *
     * @param array $cart The cart array to be saved.
     */
    public static function updateCart(array $cart) {
        // Set cookie for 7 days
        setcookie('cart', urlencode(json_encode($cart)), time() + (7 * 24 * 60 * 60), "/");
    }
    /**
     * Decrease the quantity of a cart item. Remove item if quantity becomes 0.
     *
     * @param string $productId The product ID.
     * @param string $size      The product size.
     * @param string $number    The number added to quantity.
     * @return array            The updated cart.
     */
    public static function updateCartitemQuantity($productId, $size, $number){
        $cart = Cart::getCurrentCart();;
        $found = false;
        // Loop through items to find a match.
        foreach ($cart as $i => $item) {
            if ($item[0] === $productId && $item[1] === $size) {
                if ($number>0){
                    $cart[$i][2] = $cart[$i][2] + $number;  // Increase quantity
                } else {
                    if ($item[2] > 1) {
                        $cart[$i][2] = $cart[$i][2] + $number;  // Decrease quantity
                    } else {
                        // Remove the item if quantity is 1
                        unset($cart[$i]);
                    }
                }
                $found = true;
                break;
            }
        }
        // If not found, add new item with quantity 1.
        if (!$found) {
            $cart[] = [$productId, $size, 1];
        }
        Cart::updateCart($cart);
        return $cart;
    }        
}

class ProductController extends Controller
{
    private static $path = 'product';
    public function detail($id)
    {
        print_r(Cart::getCurrentCart());
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        if ($product){
            $sizes = $productModel->getSizesByShoeId($id);
            $this->view(ProductController::$path . '/detail', ['product' => $product, 'sizes' => $sizes, 'options' => ['addCart', 'floating-button']]);
        } else{
            $this->product();
        }
    }

    public function product()
    {
        $productModel = new Product();
        if (isset($_GET['query']) && !empty($_GET['query'])) {
            $query = filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS);
            $products = $productModel->searchProductByQuery($query);
            if ($products) {
                $this->view(ProductController::$path . '/products', ['products' => $products]);
                exit;
            }
        } 
        $products = $productModel->getAllProducts();
        $this->view(ProductController::$path . '/products', ['products' => $products, 'options' => ['floating-button']]);
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
        if ($cart === null) { // cant do in runtime like py
            $cart = Cart::getCurrentCart();
        }
        $productModel = new Product();
        $cartItems = [];
        // Loop through each cart item from the cookie
        foreach ($cart as $item) {
            // Retrieve the product details using its ID
            $product = $productModel->getProductById(id: $item[0]);
            if ($product) {
                $category = $productModel->findById('categories', $product['category_id']);
                $product['quantity'] = $item[2];
                // Add the selected size and quantity to the product data
                $product['size'] = $item[1];
                $product['category'] = $category;
                // Optionally, calculate the total for this product (if price is stored per unit)
                $product['item_total'] = $product['base_price'] * $product['quantity'];
                $cartItems[] = $product;

            }
        }
        $this->view(ProductController::$path . '/cart', ['options' => ['cart'], 'cart'=>$cartItems, 'csrf_token' => Csrf::generateToken()]);
    }
}
?>