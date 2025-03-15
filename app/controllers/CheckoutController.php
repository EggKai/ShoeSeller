<?php
// app/controllers/CheckoutController.php

require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/../models/Order.php';
require_once __DIR__ . '/../models/Product.php';
require_once __DIR__ . '/../models/Cart.php';

// Make sure Stripe's autoload is included (via Composer)
require_once __DIR__ . '/../../vendor/autoload.php';
\Stripe\Stripe::setApiKey($_ENV['STRIPE_SECRET_KEY']);
class CheckoutController extends Controller{
    private const PATH = 'checkout';
    public function index() {
        $cart = Cart::getCurrentCart();
        $this->view(CheckoutController::PATH . '/index', ['options' => ['cart', 'checkout-form'], 'cart'=>Cart::fullCartDetails($cart), 'csrf_token' => Csrf::generateToken()]);
        exit;
    }
    public function checkout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        // 1. Retrieve user details.
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
        
        // 2. Retrieve the cart from the cookie.
        $cart = Cart::getCurrentCart();
        if (empty($cart)) {
            die("Cart is empty. Please add items before checking out.");
        }
        
        // 3. Build full cart details and calculate total price.
        $itemsWithPrice = Cart::fullCartDetails($cart);
        if (empty($itemsWithPrice)) {
            die("No valid items in the cart.");
        }
        $totalPrice = array_sum(array_column($itemsWithPrice, 'item_total'));
        
        // 4. Create a new pending order.
        $orderModel = new Order();
        $orderId = $orderModel->createOrder($userId, $totalPrice, $email, 'pending');
        if (!$orderId) {
            die("Error creating order.");
        }
        foreach ($itemsWithPrice as $item) {
            // We'll use $item['id'] as the product ID. Adjust if your key differs.
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
        
        // 5. Build Stripe line items array from $itemsWithPrice.
        // We'll display each product name along with its selected size.
        $stripeLineItems = [];
        foreach ($itemsWithPrice as $item) {
            // Convert base_price to cents.
            $unitAmount = (int) round($item['base_price'] * 100);
            $stripeLineItems[] = [
                'price_data' => [
                    'currency' => 'sgd', // or 'usd' depending on your store's currency
                    'unit_amount' => $unitAmount,
                    'product_data' => [
                        'name' => $item['name'] . " (Size: " . $item['size'] . ")",
                        // Optionally, add an image: 'images' => ["https://yourdomain.com/public/products/{$item['image_url']}"]
                    ],
                ],
                'quantity' => $item['quantity']
            ];
        }
        
        // 6. Create a Stripe Checkout Session.
        try {
            $domainName = $_ENV['DOMAIN'];
            $session = \Stripe\Checkout\Session::create([
                'payment_method_types' => ['card'],
                'line_items' => $stripeLineItems,
                'mode' => 'payment',
                'customer_email' => $email,
                'success_url' => "http://{$domainName}/index.php?url=checkout/success&order_id={$orderId}&session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => "http://{$domainName}/index.php?url=checkout/cancel&order_id={$orderId}",
            ]);
        } catch (Exception $e) {
            die("Stripe error: " . $e->getMessage());
        }
        
        // Optionally, you could store $session->id with the order for later verification.
        
        // 7. Clear the cart cookie only after successful payment (or via webhook)
        Cart::deleteCart();
        
        // 8. Redirect to Stripe Checkout.
        header("Location: " . $session->url, true, 303);
        exit;
    }

    public function success($orderId, $sessionId){
        try {
            // Retrieve the Checkout Session from Stripe.
            $session = \Stripe\Checkout\Session::retrieve($sessionId);
        } catch (Exception $e) {
            die("Error retrieving Checkout Session: " . $e->getMessage());
        }
        if ($session->payment_status === 'paid') {
            (new Order())->confirmOrderPayment($orderId, $sessionId);
            $this->reciept($orderId);
        } else {
            die("No."); //why would user end up here if order wasnt successful? webhook wouldve sent them elsewhere
        }
    }

    public function reciept($orderId = null) {        
        $orderModel = new Order();
        $order = $orderModel->getOrderById($orderId);
        if (!$order) {
            include __DIR__ . '/../inc/header.php';
            echo "<p>Order not found.</p>";
            include __DIR__ . '/../inc/footer.php';
            exit;
        }
        
        // Retrieve order items. Assume getOrderItems($orderId) returns an array of items.
        $orderItems = $orderModel->getOrderItems($orderId);
        
        // Pass the order and order items to the receipt view.
        $this->view(CheckoutController::PATH .'/receipt', [
            'order' => $order,
            'orderItems' => $orderItems,
            'options' => ['cart']
        ]);
        exit;
    }
}
