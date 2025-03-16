<?php
date_default_timezone_set('Asia/Singapore');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/InformationController.php';

// Define routes and their actions
$routes = [
    'products/all' => function() {
        (new ProductController())->product();
    },
    'products/detail' => function() {
        $productController = new ProductController();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id){
            if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee'])){
                (new AdminController)->updateProduct();
            } else {
                $productController->detail($id);
            }
        } else{
            $productController->product();
        }
    },
    'cart' => function(){
        (new ProductController())->cart();
    },
    'cart/minus' => function(){
        (new ProductController())->minusCartItem();
    },
    'cart/plus' => function(){
        (new ProductController())->plusCartItem();
    },
    'checkout' => function(){
        (new CheckoutController())->index();
    },
    'checkout/success' => function(){
        $orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
        $sessionId =filter_input(INPUT_GET, 'session_id', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($orderId && $sessionId){
            (new CheckoutController())->success($orderId, $sessionId);
        } else{
            (new CheckoutController())->index();
        }
    },
    'checkout/reciept' => function(){
        if (filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT)){
            (new CheckoutController())->reciept($_GET['order_id']);
        } else{
            (new CheckoutController())->index();
        }
    },
    'checkout/cancel' => function(){
        (new CheckoutController())->index();
    },
    'checkout/doCheckout' => function(){
        (new CheckoutController())->checkout();
    },
    'auth/login' => function() {
        (new UserController())->login();
    },
    'auth/doLogin' => function(){
        (new UserController())->doLogin();
    },
    'auth/register' => function(){
        (new UserController())->register();
    },
    'auth/doRegister' => function(){
        (new UserController())->doRegister();
    },
    'auth/logout' => function(){
        (new UserController())->logout();
    },
    'auth/forgotPassword' => function() {
        (new UserController())->forgotPassword();   
    },
    'information/aboutus' => function() {
        (new InformationController)->aboutus();
    },
    'information/locations' => function() {
        (new InformationController)->locations();
    },
    'admin/addProduct' => function() {
        (new AdminController)->addProduct();
    },
    'admin/doAddProduct' => function() {
        (new AdminController)->doAddProduct();
    },
    'admin/updateProduct' => function() {
        (new AdminController)->doUpdateProduct();
    },
];

// Retrieve the URL parameter (or default to home)
$url = $_GET['url'] ?? '';

// Execute the matching route if it exists; otherwise, show the home page.
if (isset($routes[$url])) {
    $routes[$url]();
} else {
    (new HomeController())->index();
}
?>