<?php 
$title = 'Cart';
include __DIR__ . '/../inc/header.php';
?>
<div class="cart-container">
  <!-- Left side: Cart Items -->
  <div class="cart-items">
    <?php if (!empty($cart)): ?>
      <?php 
      // Initialize subtotal
      $subtotal = 0;
      foreach ($cart as $item): 
        // Calculate item total and add to subtotal
        $itemTotal = $item['base_price'] * $item['quantity'];
        $subtotal += $itemTotal;
      ?>
        <div class="cart-item">
          <img src="public/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
               alt="<?php echo htmlspecialchars($item['name']); ?>">
          <div class="cart-item-details">
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p><?php echo htmlspecialchars($item['category']['name']); ?></p>
            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
          </div>
          <div class="cart-item-actions">
            <div class="quantity-controls">
              <button class="qty-minus" >-</button>
              <input type="number" class="qty-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1">
              <button class="qty-plus">+</button>
            </div>
            <div class="cart-item-price">$<?php echo number_format($itemTotal, 2); ?></div>
          </div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Your cart is empty.</p>
    <?php endif; ?>
  </div>

  <!-- Right side: Summary -->
  <div class="cart-summary">
    <h2>Summary</h2>
    <div class="summary-row">
      <span>Subtotal</span>
      <span>$<?php echo number_format($subtotal ?? 0, 2); ?></span>
    </div>
    <div class="summary-row">
      <span>Estimated Delivery & Handling</span>
      <span>Free</span>
    </div>
    <div class="summary-row summary-total">
      <span>Total</span>
      <span>$<?php echo number_format($subtotal ?? 0, 2); ?></span>
    </div>

    <div class="checkout-buttons">
      <button class="guest-checkout">Guest Checkout</button>
      <button class="member-checkout">Member Checkout</button>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>