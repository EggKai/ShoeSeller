<?php
date_default_timezone_set('Asia/Singapore');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';

// Define routes and their actions
$routes = [
    'products/all' => function() {
        (new ProductController())->product();
    },
    'products/detail' => function() {
        $productController = new ProductController();
        if (filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT)){
            $productController->detail($_GET['id']);
        } else{
            $productController->product();
        }
    },
    'cart' => function(){
        (new ProductController())->cart();
    },
    'auth/login' => function() {
        // Call the appropriate method in UserController (e.g., login)
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
    'admin/addProduct' => function() {
        (new AdminController)->addProduct();
    },
    'admin/doAddProduct' => function() {
        (new AdminController)->doAddProduct();
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