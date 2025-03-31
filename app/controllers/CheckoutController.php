<?php
// app/controllers/CheckoutController.php

require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/ProductController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';
require_once __DIR__ . '/../../core/email.php';
require_once __DIR__ . '/../../core/log.php';

// Make sure Stripe's autoload is included (via Composer)
require_once __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
class CheckoutController extends Controller
{
    private const PATH = 'checkout';
    public function index()
    {
        $cart = Cart::getCurrentCart();
        $this->view(CheckoutController::PATH . '/index', ['options' => ['cart', 'checkout-form', 'form'], 'cart' => Cart::fullCartDetails($cart), 'csrf_token' => Csrf::generateToken()]);
        exit;
    }
    public function checkout()
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            (new HomeController)->index();
            exit;
        }
        if (isset($_SESSION['user'])) {
            $userId = $_SESSION['user']['id'];  // logged-in user ID
            $email = $_SESSION['user']['email'] ?? '';
        } else {
            $userId = null; // Guest
            $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        }
        if (empty($email)) {
            die("No email provided. Please log in or provide an email to continue.");
        }
        $cart = Cart::getCurrentCart(); // Retrieve the cart from the cookie.
        if (empty($cart)) {
            die("Cart is empty. Please add items before checking out.");
        }

        $itemsWithPrice = Cart::fullCartDetails($cart); // Build full cart details and calculate total price.
        if (empty($itemsWithPrice)) {
            die("No valid items in the cart.");
        }
        $totalPrice = array_sum(array_column($itemsWithPrice, 'item_total'));
        $productModel = new Product();

        foreach ($itemsWithPrice as $item) {
            $availableStock = $productModel->getStockForItem($item['id'], $item['size']);
            if ($item['quantity'] > $availableStock) {
                (new ProductController)->cart($cart, ["Insufficient stock for {$item['name']} (Size: {$item['size']}). Available: {$availableStock}, Requested: {$item['quantity']}", 2]);
            }
        }
        $orderModel = new Order();
        $orderId = $orderModel->createOrder($userId, $totalPrice, $email, isset($_POST['usepoints'])); // Create a new pending order.
        if (!$orderId) {
            die("Error creating order.");
        }
        foreach ($itemsWithPrice as $item) {
            $success = $orderModel->addOrderItem(
                $orderId,
                $item['id'],
                $item['size'],
                $item['quantity'],
                $item['base_price']
            );
            if (!$success) {
                continue; // ignore error
            }
        }
        $stripeLineItems = [];
        foreach ($itemsWithPrice as $item) {  // Build Stripe line items array from $itemsWithPrice.
            // Convert base_price to cents.
            $unitAmount = (int) round($item['base_price'] * 100);
            $stripeLineItems[] = [
                'price_data' => [
                    'currency' => 'sgd',
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => $item['name'] . " (Size: " . $item['size'] . ")",
                        // TODO add images (needs domain)
                        // 'images' => ["https://{$domain}.com/public/products/{$item['image_url']}"]
                    ],
                ],
                'quantity' => $item['quantity']
            ];
        }
        $discountsArray = [];
        if (isset($_POST['usepoints'])) {
            if ($_SESSION['user']['points'] > 0) {
                try {
                    $coupon = \Stripe\Coupon::create([
                        'amount_off' => round($_SESSION['user']['points']), // discount in cents
                        'currency' => 'sgd',
                        'duration' => 'once'
                    ]);
                    $discountsArray[] = ['coupon' => $coupon->id];
                } catch (Exception $e) {
                    die("Error creating discount coupon: " . $e->getMessage());
                }
            }
        }

        try {  // Create a Stripe Checkout Session.
            $domainName = $_ENV['DOMAIN'];
            $protocol = $_ENV['PROTOCOL'];
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $stripeLineItems,
                'mode' => 'payment',
                'customer_email' => $email,
                'discounts' => $discountsArray,
                'success_url' => "{$protocol}://{$domainName}/index.php?url=checkout/success&order_id={$orderId}&session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => "{$protocol}://{$domainName}/index.php?url=checkout/cancel&order_id={$orderId}",
            ]);
        } catch (Exception $e) {
            die("Stripe error: " . $e->getMessage());
        }
        if (isset($_POST['usepoints']) && $_SESSION['user']['points'] > 0) {
            (new Auth)->clearPoints($_SESSION['user']['id']);
        }
        $orderModel->update_sessionId($orderId, $session->id);
        logAction("INFO Order #$orderId pending payment");
        header("Location: " . $session->url, true, 303); // Redirect to Stripe Checkout.
        exit;
    }

    public function success($orderId, $sessionId)
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderById($orderId);
        if (!$order) {
            die("Order not found.");
        }
        if ($order['status'] === 'pending') {
            try {
                // Retrieve the Checkout Session from Stripe.
                $session = \Stripe\Checkout\Session::retrieve($sessionId);
            } catch (Exception $e) {
                die("Error retrieving Checkout Session: " . $e->getMessage());
            }
            if (!($session->payment_status === 'paid')) {
                die("No."); //why would user end up here if order wasnt successful? webhook wouldve sent them elsewhere
            }
            if (!$orderModel->confirmOrderPayment($orderId, $sessionId)) {
                die("Invalid Session"); //session id does not match actual ID 
            }
            $orderItems = $orderModel->getOrderItems($orderId);
            $productModel = new Product();
            foreach ($orderItems as $item) { // Reduce stock for each purchased item.
                if (!$productModel->reduceStock($item['product_id'], $item['size'], $item['quantity'])) {
                    logError("Failed to update stock for product {$item['product_id']} size {$item['size']}.");
                }
            }
            $userModel = new Auth;
            $userModel->addPoints($order['user_id'], $order['total_price']);
            $userModel->refreshUser();
            logAction("INFO Order #$orderId confirmed Payment");
            Cart::deleteCart();
            sendReceiptEmail($orderModel->getOrderById($orderId), $orderModel->getOrderItems($orderId));
        }
        $this->reciept($orderId);
        exit;
    }

    public function reciept($orderId = null)
    {
        $orderModel = new Order();
        $order = $orderModel->getOrderById($orderId);
        if (!$order) {
            include __DIR__ . '/../inc/header.php';
            echo "<p>Order not found.</p>";
            include __DIR__ . '/../inc/footer.php';
            exit;
        }

        $orderItems = $orderModel->getOrderItems($orderId); // Retrieve order items. Assume getOrderItems($orderId) returns an array of items.

        // Pass the order and order items to the receipt view.
        $this->view(CheckoutController::PATH . '/receipt', [
            'order' => $order,
            'orderItems' => $orderItems,
            'options' => ['cart']
        ]);
        exit;
    }
}
