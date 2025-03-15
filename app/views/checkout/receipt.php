<?php
$title = "Order Receipt";
include __DIR__ . '/../inc/header.php';
?>
<div class="cart-container">
  <!-- Left side: Order Items -->
  <div class="cart-items">
    <?php if (!empty($orderItems)): ?>
      <?php
      // Optionally, recalculate subtotal from order items if needed.
      $calculatedSubtotal = 0;
      foreach ($orderItems as $item):
          $itemTotal = $item['price'] * $item['quantity'];
          $calculatedSubtotal += $itemTotal;
      ?>
        <div class="cart-item">
          <img src="public/products/<?php echo htmlspecialchars($item['image_url']); ?>" 
               alt="<?php echo htmlspecialchars($item['name']); ?>">
          <div class="cart-item-details">
            <h3><?php echo htmlspecialchars($item['name']); ?></h3>
            <p>Size: <?php echo htmlspecialchars($item['size']); ?></p>
            <p>Quantity: <?php echo htmlspecialchars($item['quantity']); ?></p>
          </div>
          <div class="cart-item-price">$<?php echo number_format($itemTotal, 2); ?></div>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <p>Your order is empty.</p>
    <?php endif; ?>
  </div>

  <!-- Right side: Order Summary -->
  <div class="cart-summary">
    <h2>Order Summary</h2>
    <div class="summary-row">
      <span>Subtotal</span>
      <span>$<?php echo number_format($order['total_price'], 2); ?></span>
    </div>
    <div class="summary-row">
      <span>Email</span>
      <span><?php echo htmlspecialchars($order['email']); ?></span>
    </div>
    <div class="summary-row">
      <span>Order Date</span>
      <span><?php echo htmlspecialchars($order['created_at']); ?></span>
    </div>
    <div class="summary-row summary-total">
      <span>Total</span>
      <span>$<?php echo number_format($order['total_price'], 2); ?></span>
    </div>
  </div>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>
