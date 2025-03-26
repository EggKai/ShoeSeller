<?php
$title = "All products";
include __DIR__ . '/../inc/header.php';
?>
<div class="product-container __content">
  <?php
  if ($products) {
    foreach ($products as $product) { ?>
      <div class="product-item">
        <!-- Image -->
        <a href="index.php?url=products/detail&id=<?php echo urlencode($product['id']); ?>">
          <img src="public/products/<?php echo htmlspecialchars($product['image_url']); ?>"
            alt="<?php echo htmlspecialchars($product['name']); ?>" />
          <!-- Product Name -->
          <h3 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h3>
          <h6 class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></h6>
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