<?php
$title = "Checkout";
$description = 'Check out cart items';
include __DIR__ . '/../inc/header.php';

// Calculate subtotal based on cart items.
$subtotal = 0;
if (!empty($cart)) {
    foreach ($cart as $item) {
        $itemTotal = $item['base_price'] * $item['quantity'];
        $subtotal += $itemTotal;
    }
}
?>
<div class="checkout-container">
    <h1>Checkout</h1>
    <!-- Cart Summary Section -->
    <div class="checkout-cart">
        <?php if (!empty($cart)): ?>
            <div class="cart-items">
                <?php foreach ($cart as $item): 
                    $itemTotal = $item['base_price'] * $item['quantity'];
                ?>
                    <div class="cart-item">
                        <img src="public/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="cart-item-details">
                            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
                            <p><?php echo htmlspecialchars($item['category']['name']); ?></p>
                            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
                            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
                        </div>
                        <div class="cart-item-price">$<?php echo number_format($itemTotal, 2); ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="cart-summary">
                <h2>Summary</h2>
                <div class="summary-row">
                    <span>Subtotal</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
                <div class="summary-row">
                    <span>Estimated Delivery & Handling</span>
                    <span>Free</span>
                </div>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($subtotal, 2); ?></span>
                </div>
            </div>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
    
    <!-- Customer Details Form -->
    <?php if (!empty($cart)): ?>
        <div class="checkout-details">
            <h2>Enter Your Details</h2>
            <form action="index.php?url=checkout/doCheckout" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required 
                           value="<?php echo htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea name="address" id="address" rows="4" required><?php echo htmlspecialchars($data['address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
