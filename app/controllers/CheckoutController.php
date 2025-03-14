<?php
// app/controllers/CheckoutController.php

require_once __DIR__ . '/HomeController.php';
require_once __DIR__ . '/../app/models/Order.php';
require_once __DIR__ . '/../app/models/Product.php';
require_once __DIR__ . '/../app/models/Cart.php';

class CheckoutController {
    public function checkout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        // 1. Get user ID if logged in, otherwise 0 for guest.
        $userId = $_SESSION['user']['user_type'] ?? 'guest';
        if ($userId === 'user'){
            $email = $_SESSION['user']['email'] ?? ($_POST['email'] ?? '');
            if (empty($email)) {
                die("No email provided. Please log in or provide an email to continue."); // 2. Get user email (if logged in) or from a POST form for guest checkout.
            }
        }
        
        // 3. Retrieve the cart from the cookie.
        $cart = Cart::getCurrentCart();
        if (empty($cart)) {
            die("Cart is empty. Please add items before checking out.");
        }

        // 4. Calculate total price and build a list of items with prices.
        $itemsWithPrice = Cart::fullCartDetails($cart);
        $totalPrice = $totalPrice = array_sum(array_column($itemsWithPrice, 'item_total'));;
        if (empty($itemsWithPrice)) {
            die("No valid items in the cart.");
        }
        // 5. Create a new order
        $orderModel = new Order();
        $orderId = $orderModel->createOrder($userId, $totalPrice, $email, 'pending');
        if (!$orderId) {
            die("Error creating order.");
        }
        // 6. Insert each item into order_items
        foreach ($itemsWithPrice as $item) {
            $success = $orderModel->addOrderItem($orderId, $item['product_id'], $item['size'], $item['quantity'], $item['price']);
            if (!$success) {
                // Optionally log or handle item insertion errors
                // For now, we just continue
            }
        }
        // 8. show confirmation
        $this->success($orderId);
        exit;
    }

    public function success($id = null) { // index.php?url=checkout/success&order_id=$orderId
        // A simple success page
        if ($id === null){
            $orderId = $_GET['order_id'] ?? 0;
        }
        echo "Order #$orderId placed successfully!";
    }
}
