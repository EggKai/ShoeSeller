<?php
date_default_timezone_set('Asia/Singapore');
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
require_once __DIR__ . '/../core/CORs.php';
require_once __DIR__ . '/../app/controllers/HomeController.php';
require_once __DIR__ . '/../app/controllers/ProductController.php';
require_once __DIR__ . '/../app/controllers/UserController.php';
require_once __DIR__ . '/../app/controllers/CheckoutController.php';
require_once __DIR__ . '/../app/controllers/EmployeeController.php';
require_once __DIR__ . '/../app/controllers/AdminController.php';
require_once __DIR__ . '/../app/controllers/InformationController.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/controllers/ReviewController.php';
require_once __DIR__ . '/../app/models/RememberToken.php';

$routes = [ // Define routes and their actions
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
                (new EmployeeController())->updateProduct();
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
            $productController->createReview($id);
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
    'checkout/reCheckout' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new CheckoutController())->reCheckout();
    },
    'auth/login' => function () {
        if (isset($_SESSION['user'])) {
            (new UserController())->profile();
            exit;
        }
        (new UserController())->login();
    },
    'auth/doLogin' => function () {
        if (isset($_SESSION['user'])) {
            (new UserController())->profile();
            exit;
        }
        (new UserController())->doLogin();
    },
    'auth/register' => function () {
        (new UserController())->register();
    },
    'auth/doRegister' => function () {
        if (isset($_SESSION['user'])) {
            (new UserController())->profile();
            exit;
        }
        (new UserController())->doRegister();
    },
    'auth/profile' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new UserController())->profile();
    },
    'auth/orderHistory' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new UserController())->orderHistory();
    },
    
    'auth/logout' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new UserController())->logout();
    },
    'auth/forgotPassword' => function () {
        (new UserController())->forgotPassword();
    },
    'auth/resetPassword' => function () {
        (new UserController())->resetPassword();
    },
    'auth/reset_password' => function () {
        (new UserController())->reset();
    },
    'auth/doReset' => function () {
        (new UserController())->doReset();
    },
    'auth/editProfile' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new UserController())->editProfile();
    },
    'auth/doEditProfile' => function () {
        if (!isset($_SESSION['user'])) {
            (new UserController())->login();
            exit;
        }
        (new UserController())->doEditProfile();
    },
    'information/aboutus' => function () {
        (new InformationController)->aboutus();
    },
    'information/contactus' => function () {
        (new InformationController)->contactus();
    },
    'information/rs-policy' => function () {
        (new InformationController)->rsPolicy();
    },
    'information/corporate-information' => function () {
        (new InformationController)->corporateInformation();
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
    'information/privacy-policy' => function () {
        (new InformationController)->privacyPolicy();  
    },
    'information/regulatory-framework' => function () {
        (new InformationController)->Regulatoryframework();  
    },
    'information/accessibility' => function () {
        (new InformationController)->accessibility();  
    },
    'information/paymentmethods' => function () {
        (new InformationController)->paymentmethods();  
    },
    'information/FAQ' => function () {
        (new InformationController)->faq();
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
    'employee/addProduct' => function () {
        (new EmployeeController())->addProduct();
    },
    'employee/doAddProduct' => function () {
        (new EmployeeController())->doAddProduct();
    },
    'employee/updateProduct' => function () {
        (new EmployeeController())->doUpdateProduct();
    },
    'employee/handleListing' => function () {
        (new EmployeeController())->handleListing();
    },
    'admin/dashboard' => function () {
        (new AdminController())->dashboard();
    },
    'admin/users' => function () {
        (new AdminController())->viewUsers();
    },
    'admin/deleteUser' => function () {
        (new AdminController())->deleteUser();
    },
    'admin/createUser' => function () {
        (new AdminController())->createUser();
    },
    'admin/doCreateUser' => function () {
        (new AdminController())->doCreateUser();
    },
    'admin/viewLogs' => function () {
        (new AdminController())->viewLogs();
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