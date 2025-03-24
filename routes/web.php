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
require_once __DIR__ . '/../app/controllers/ReviewController.php';

// Define routes and their actions
$routes = [
    'products/all' => function () {
        if (isset($_GET['query']) && !empty(filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS))) {
            (new ProductController())->product(filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS));
        }
        (new ProductController())->product();
    },
    'products/detail' => function () {
        $productController = new ProductController();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee'])) {
                (new AdminController)->updateProduct();
            } else {
                $productController->detail($id);
            }
        } else {
            $productController->product();
        }
    },
    'products/createReview' => function() {
        $productController = new ProductController();
        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if ($id) {
            $productController->createReview();
            exit;
        }
        $productController->product();
    },
    'cart' => function () {
        (new ProductController())->cart();
    },
    'cart/minus' => function () {
        (new ProductController())->minusCartItem();
    },
    'cart/plus' => function () {
        (new ProductController())->plusCartItem();
    },
    'checkout' => function () {
        (new CheckoutController())->index();
    },
    'checkout/success' => function () {
        $orderId = filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT);
        $sessionId = filter_input(INPUT_GET, 'session_id', FILTER_SANITIZE_SPECIAL_CHARS);
        if ($orderId && $sessionId) {
            (new CheckoutController())->success($orderId, $sessionId);
        } else {
            (new CheckoutController())->index();
        }
    },
    'checkout/receipt' => function () {
        if (filter_input(INPUT_GET, 'order_id', FILTER_VALIDATE_INT)) {
            (new CheckoutController())->reciept($_GET['order_id']);
        } else {
            (new CheckoutController())->index();
        }
    },
    'checkout/cancel' => function () {
        (new CheckoutController())->index();
    },
    'checkout/doCheckout' => function () {
        (new CheckoutController())->checkout();
    },
    'auth/login' => function () {
        (new UserController())->login();
    },
    'auth/doLogin' => function () {
        (new UserController())->doLogin();
    },
    'auth/register' => function () {
        (new UserController())->register();
    },
    'auth/doRegister' => function () {
        (new UserController())->doRegister();
    },
    'auth/profile' => function () {
        if (!isset($_SESSION['user'])) {
            // if ($_SESSION['user']['user_type'] !== 'user')
            (new UserController())->login();
            exit;
        }
        (new UserController())->profile();
    },
    'auth/logout' => function () {
        (new UserController())->logout();
    },
    'auth/forgotPassword' => function () {
        (new UserController())->forgotPassword();
    },
    'information/aboutus' => function () {
        (new InformationController)->aboutus();
    },
    'information/locations' => function () {
        (new InformationController)->locations();  
    },
    'information/terms-and-conditions' => function () {
        (new InformationController)->termsAndConditions();  
    },
    'information/cookie-policy' => function () {
        (new InformationController)->cookiePolicy();  
    },
    'information/cookie-preference' => function () {
        (new InformationController)->cookiepreference();  
    },
    'information/privacy-policy' => function () {
        (new InformationController)->privacyPolicy();  
    },
    'information/regulatory-framework' => function () {
        (new InformationController)->Regulatoryframework();  
    },
    'information/accessibility' => function () {
        (new InformationController)->accessibility();  
    },

    'information/doAddLocation' => function () {
        (new InformationController)->doAddLocation();
    },
    'information/doUpdateLocation' => function () {
        (new InformationController)->doUpdateLocation();
    },
    'information/doRemoveLocation' => function () {
        (new InformationController)->doRemoveLocation();
    },
    'admin/addProduct' => function () {
        (new AdminController)->addProduct();
    },
    'admin/dashboard' => function () {
        (new AdminController)->dashboard();
    },
    'admin/doAddProduct' => function () {
        (new AdminController)->doAddProduct();
    },
    'admin/updateProduct' => function () {
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