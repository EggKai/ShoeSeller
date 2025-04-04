<?php
$title = "All products";
$description = "All products Page";
include __DIR__ . '/../inc/header.php';
?>
<h1 class="visually-hidden">Your Cart</h1>
<div class="product-container __content">
  <?php
  if ($products) {
    foreach ($products as $product) { ?>
      <div class="product-item">
        <!-- Image -->
        <a href="/products/detail&id=<?php echo urlencode($product['id']); ?>">
          <img src="/public/products/<?php echo htmlspecialchars($product['image_url'], ENT_COMPAT, 'UTF-8'); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>" />
          <!-- Product Name -->
          <h2 class="product-title"><?php echo htmlspecialchars($product['name'], ENT_COMPAT, 'UTF-8'); ?></h2>
          <h3 class="product-brand"><?php echo htmlspecialchars($product['brand'], ENT_COMPAT, 'UTF-8'); ?> <?php echo $product['unlisted']?'<span class="red">Unlisted</span>':'' ?></h3>
          <!-- Product Price -->
          <p class="product-price">$<?php echo number_format($product['base_price'], 2); ?></p>

        </a>
      </div>
    <?php }
  } else { ?>
    <h1>
      No Items Found
    </h1>
  <?php } ?>
</div>
<?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee']) ) { ?>
  <a href="index.php?url=employee/addProduct"><button class="floating-plus-button">+</button></a>
<?php } ?>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>