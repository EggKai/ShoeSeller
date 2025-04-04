<?php
$title = 'Cart';
include __DIR__ . '/../inc/header.php';
?>
<h1 class="visually-hidden">Your Cart</h1>
<div class="cart-container">
  <?php  include __DIR__ . '/../partials/alert.php'; ?>
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
            <h2><?php echo htmlspecialchars($item['name']); ?></h2>
            <p><?php echo htmlspecialchars($item['category']['name']); ?></p>
            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
          </div>
          <div class="cart-item-actions">
            <div class="quantity-controls">
              <!-- Minus form -->
              <form action="/index.php?url=cart/minus" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                <input type="hidden" name="size" value="<?php echo htmlspecialchars($item['size']); ?>">
                <button class="qty-minus" type="submit" aria-label="Decrease quantity of <?php echo htmlspecialchars($item['name']); ?>">-</button>
              </form>
              <input type="number" class="qty-input" value="<?php echo htmlspecialchars($item['quantity']); ?>" min="1" readonly 
                     aria-label="Quantity of <?php echo htmlspecialchars($item['name']); ?>">
              <!-- Plus form -->
              <form action="/index.php?url=cart/plus" method="POST" style="display:inline;">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token); ?>">
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($item['id']); ?>">
                <input type="hidden" name="size" value="<?php echo htmlspecialchars($item['size']); ?>">
                <button class="qty-plus" type="submit" aria-label="Increase quantity of <?php echo htmlspecialchars($item['name']); ?>">+</button>
              </form>
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
      <?php if (!isset($_SESSION['user'])): ?>
        <!-- If not logged in, show both Guest and Member checkout buttons -->
        <a href="/checkout">
          <button class="guest-checkout" id="guestCheckout">Guest Checkout</button>
        </a>
        <a href="/auth/login">
          <button type="button" class="member-checkout">Member Checkout</button>
        </a>
      <?php else: ?>
        <!-- If logged in, show a single checkout button -->
        <a href="/checkout">
          <button class="guest-checkout" id="memberCheckout">Checkout</button>
        </a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
