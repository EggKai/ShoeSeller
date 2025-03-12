<?php
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../../core/Controller.php';

class ProductController extends Controller
{
    private static $path = 'product';
    public function detail($id)
    {
        $productModel = new Product();
        $product = $productModel->getProductById($id);
        $sizes = $productModel->getSizesByShoeId($id);
        $this->view(ProductController::$path . '/detail', ['product' => $product, 'sizes' => $sizes, 'options' => ['addCart', 'floating-button']]);
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

    public function cart()
    {
        $cart = [];
        if (isset($_COOKIE['cart'])) {
            $decoded = json_decode(urldecode($_COOKIE['cart']), true);
            if (is_array($decoded)) {
                $cart = $decoded;
            }
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
        $this->view(ProductController::$path . '/cart', ['options' => ['cart'], 'cart'=>$cartItems]);
    }
}
?>