<?php
$title = "Order History";
include __DIR__ . '/../inc/header.php';
?>
<div class="order-history-container">
    <h1>Order History</h1>
    <?php if (!empty($orders)): ?>
        <table class="orders-table">
            <thead>
                <tr>
                    <th>Order ID</th>
                    <th>Date</th>
                    <th>Total Price</th>
                    <th>Discount</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orders as $order): ?>
                    <tr>
                        <td>
                            <?php if (strtolower($order['status']) !== 'pending'): ?>
                                <a href="index.php?url=checkout/receipt&order_id=<?php echo htmlspecialchars($order['id']); ?>">
                                    <?php echo htmlspecialchars($order['id']); ?>
                                </a>
                            <?php else: ?>
                                <?php echo htmlspecialchars($order['id']); ?>
                            <?php endif; ?>
                        </td>
                        <td><?php echo htmlspecialchars($order['created_at']); ?></td>
                        <td>$<?php echo number_format($order['total_price'], 2); ?></td>
                        <td>$<?php echo number_format($order['discount']/100, 2); ?></td>
                        <td><?php echo htmlspecialchars($order['status']); ?></td>
                        <td>
                            <?php if (strtolower($order['status']) === 'pending'): ?>
                                <a href="/checkout/reCheckout&id=<?php echo htmlspecialchars($order['id']); ?>" class="btn pending-action">
                                    Pay
                                </a>
                            <?php else: ?>
                                <span class="no-action">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>You have no orders.</p>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
