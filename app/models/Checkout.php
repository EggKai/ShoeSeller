<?php
require_once __DIR__ . '/../../core/Model.php';

class Order extends Model
{
    /**
     * Create a new order in the 'orders' table and update user points.
     *
     * If $usePoints is true, the user's available points will be redeemed for a discount
     * (1 point = 1 cent) and the discount amount is stored in the 'discount' field.
     * Regardless of redemption, a registered user gains new points based on the original total.
     *
     * @param int    $userId      The ID of the logged-in user (or 0 for guest).
     * @param float  $totalPrice  The total price of the order before discount.
     * @param string $email       The user's or guest's email.
     * @param bool   $usePoints   Whether to redeem all available points for a discount.
     * @param string $status      Order status; default is 'pending'.
     * @return int|false          The newly created order ID or false on failure.
     */
    public function createOrder($userId, $totalPrice, $email, $usePoints = false, $status = 'pending')
    {
        $discount = 0;
        if ($usePoints) {
            $discount = number_format($usePoints / 100, 2);
        }
        // Insert the order with the discounted total and discount amount.
        $stmt = $this->pdo->prepare("
        INSERT INTO orders (user_id, total_price, discount, status, email ,created_at)
        VALUES (:user_id, :total_price, :discount, :status, :email, NOW())
        ");
        $params = [
            'user_id' => $userId,
            'total_price' => $totalPrice,
            'discount' => $discount,
            'status' => $status,
            'email' => $email
        ];

        if ($stmt->execute($params)) {
            $orderId = $this->pdo->lastInsertId();
            return $orderId;
        }
        return false;
    }

    /**
     * Add an item to the 'order_items' table.
     *
     * @param int    $orderId   The ID of the order.
     * @param int    $productId The ID of the product.
     * @param float  $size      The selected shoe size.
     * @param int    $quantity  The quantity of items purchased.
     * @param float  $price     The unit price at the time of purchase.
     * @return bool             True on success, false on failure.
     */
    public function addOrderItem($orderId, $productId, $size, $quantity, $price)
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, size, quantity, price)
            VALUES (:order_id, :product_id, :size, :quantity, :price)
        ");
        $params = [
            'order_id' => $orderId,
            'product_id' => $productId,
            'size' => $size,
            'quantity' => $quantity,
            'price' => $price
        ];
        return $stmt->execute($params);
    }
}
