<?php
require_once __DIR__ . '/../../core/Model.php';

class Order extends Model {
    /**
     * Create a new order in the 'orders' table.
     *
     * @param int    $userId      The ID of the logged-in user (or 0 for guest).
     * @param float  $totalPrice  The total price of this order.
     * @param string $email       The user's or guest's email.
     * @param string $status      Default to 'pending'.
     * @return int|false          The newly created order ID or false on failure.
     */
    public function createOrder($userId, $totalPrice, $email, $status = 'pending') {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (user_id, total_price, status, email, created_at)
            VALUES (:user_id, :total_price, :status, :email, NOW())
        ");
        $params = [
            'user_id'     => $userId,
            'total_price' => $totalPrice,
            'status'      => $status,
            'email'       => $email
        ];
        if ($stmt->execute($params)) {
            return $this->pdo->lastInsertId();
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
    public function addOrderItem($orderId, $productId, $size, $quantity, $price) {
        $stmt = $this->pdo->prepare("
            INSERT INTO order_items (order_id, product_id, size, quantity, price)
            VALUES (:order_id, :product_id, :size, :quantity, :price)
        ");
        $params = [
            'order_id'   => $orderId,
            'product_id' => $productId,
            'size'       => $size,
            'quantity'   => $quantity,
            'price'      => $price
        ];
        return $stmt->execute($params);
    }
}
