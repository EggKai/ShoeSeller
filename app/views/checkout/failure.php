<?php
$title = "Payment Failed";
include __DIR__ . '/../inc/header.php';
?>
<div class="payment-failure">
  <h1>Payment Failed</h1>
  <p>We couldn't process your payment at this time. Please check your payment details or try again later.</p>
  <p>If you need assistance, please <a href="/index.php?url=support/contact">contact our support team</a>.</p>
  <a href="/index.php?url=cart" class="btn">Return to Cart</a>
</div>
<?php include __DIR__ . '/../inc/footer.php'; ?>