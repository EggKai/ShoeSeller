<?php 
$title = "All products";
include __DIR__ . '/../inc/header.php';
?>
<div class="product-container">
    <?php foreach ($products as $product): ?>
      <div class="product-item">
        <!-- Image -->
        <a href="index.php?url=products/detail&id=<?php echo urlencode($product['id']); ?>">
        <img 
          src="public/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
          alt="<?php echo htmlspecialchars($product['name']); ?>" 
        />

        <!-- Product Name -->
        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
        <h6><?php echo htmlspecialchars($product['brand']); ?></h6>

        <!-- Product Price -->
        <p>$<?php echo number_format($product['base_price'], 2); ?></p>

        </>
      </div>
    <?php endforeach; ?>
  </div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>
