<?php
$title = "Checkout";
$description = 'Check out cart items';
include __DIR__ . '/../inc/header.php';

// Determine if user wants to claim points.
$claimPoints = false;
$discount = 0;
if (isset($_SESSION['user']) && $_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['claimPoints']) && $_SESSION['user']['points'] != 0) {
    $claimPoints = true;
    $discount = number_format($_SESSION['user']['points']/100, 2);
}

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
                        <img src="/public/products/<?php echo htmlspecialchars($item['image_url']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
                        <div class="cart-item-details">
                            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
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
                <?php if ($claimPoints){ ?>
                    <div class="summary-row">
                        <span>Discount</span>
                        <span>$<?php echo $discount; ?></span>
                    </div>
                <?php } ?>
                <div class="summary-row summary-total">
                    <span>Total</span>
                    <span>$<?php echo number_format($subtotal-$discount, 2); ?></span>
                </div>
            </div>
            <?php
            // If user is logged in, show the "claim points" button if they haven't already claimed.
            if (isset($_SESSION['user']) && $_SESSION['user']['points'] != 0 && !$claimPoints) { ?>
                <form action="" method="POST" class="claim-points-form">
                    <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                    <input type="hidden" name="claimPoints" value="true">
                    <button type="submit" class="claim-points-btn">
                        Claim Your <?php echo $_SESSION['user']['points']; ?> Points for Discount
                    </button>
                </form>
            <?php } ?>
        <?php else: ?>
            <p>Your cart is empty.</p>
        <?php endif; ?>
    </div>
    
    <!-- Customer Details Form -->
    <?php if (!empty($cart)): ?>
        <div class="checkout-details">
            <h2>Enter Your Details</h2>
            <form action="/checkout/doCheckout" method="POST">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <?php if ($claimPoints){ ?>
                    <input type="hidden" name="usepoints" value="true">
                <?php } ?>
                <div class="form-group">
                    <label for="email">Email:</label>
                    <input type="email" name="email" id="email" required 
                           value="<?php echo $_SESSION['user']['email'] ?? htmlspecialchars($data['email'] ?? ''); ?>">
                </div>
                <div class="form-group">
                    <label for="address">Address:</label>
                    <textarea name="address" id="address" rows="4" required><?php echo $_SESSION['user']['address'] ?? htmlspecialchars($data['address'] ?? ''); ?></textarea>
                </div>
                <button type="submit" class="checkout-btn">Place Order</button>
            </form>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../inc/footer.php'; ?>
