<?php
require_once __DIR__ . '/../../core/Model.php';

class Order extends Model
{

    public function getOrderById($id)
    {
        return $this->findById('orders', $id);
    }
    public function getOrderItemsByOrderId($orderId)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM order_items WHERE order_id = :order_id");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    /**
     * Create a new order in the 'orders' table.
     *
     * @param int    $userId      The ID of the logged-in user (or 0 for guest).
     * @param float  $totalPrice  The total price of this order.
     * @param string $email       The user's or guest's email.
     * @param string $status      Default to 'pending'.
     * @return int|false          The newly created order ID or false on failure.
     */
    public function createOrder($userId, $totalPrice, $email, $discount=0,$status = 'pending')
    {
        $stmt = $this->pdo->prepare("
            INSERT INTO orders (user_id, total_price, status, email, discount, created_at)
            VALUES (:user_id, :total_price, :status, :email, :discount, NOW())
        ");
        $params = [
            'user_id' => $userId,
            'total_price' => $totalPrice,
            'status' => $status,
            'email' => $email,
            'discount' => $discount
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
    public function getOrderItems($orderId)
    {
        $stmt = $this->pdo->prepare("
            SELECT oi.*, p.name, p.image_url , p.base_price
            FROM order_items oi
            JOIN products p ON oi.product_id = p.id
            WHERE oi.order_id = :order_id
        ");
        $stmt->execute(['order_id' => $orderId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function update_sessionId($orderId, $sessionId)
    {
        $stmt = $this->pdo->prepare("
        UPDATE orders
        SET session_id = :session_id
        WHERE id = :id
    ");
        return $stmt->execute([
            'session_id' => $sessionId,
            'id' => $orderId
        ]);
    }
    /**
     * Update the order record to set the session_id and mark it as shipped.
     *
     * @param int    $orderId    The order's ID.
     * @param string $sessionId  The Stripe session ID to store.
     * @return bool              True on success, false on failure.
     */
    public function confirmOrderPayment($orderId, $sessionId)
    {
        $stmt = $this->pdo->prepare("
        UPDATE orders
        SET status = 'paid'
        WHERE id = :id AND session_id = :session_id
    ");
        return $stmt->execute([
            'session_id' => $sessionId,
            'id' => $orderId
        ]);
    }

    public function getOrdersByUserId($userId) {
        $stmt = $this->pdo->prepare("
            SELECT * FROM orders 
            WHERE user_id = :user_id 
            ORDER BY created_at DESC
        ");
        $stmt->execute(['user_id' => $userId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
