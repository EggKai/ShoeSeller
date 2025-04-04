<?php
$title = htmlspecialchars($product['name'], ENT_COMPAT, 'UTF-8');
$description = 'Details for product:'.htmlspecialchars($product['name'], ENT_COMPAT, 'UTF-8').', Brand:'. htmlspecialchars($product['brand']).' ,with a price of $'.number_format($product['base_price'], 2);
include __DIR__ . '/../inc/header.php';
?>
<section class="__content">
<div class="product-page">
  <!-- LEFT COLUMN: Product Image -->
  <div class="product-image">
    <img src="/public/products/<?php echo htmlspecialchars($product['image_url']); ?>"
      alt="<?php echo htmlspecialchars($product['name']); ?>">
  </div>

  <!-- RIGHT COLUMN: Product Details -->
  <div class="product-info">
    <!-- Regular Display for Customers -->
    <h1 class="product-name"><?php echo htmlspecialchars($product['name'], ENT_COMPAT, 'UTF-8'); ?></h1>
    <h2 class="product-category"><?php echo htmlspecialchars($category['name'], ENT_COMPAT, 'UTF-8'); ?></h2>
    <h3 class="product-brand"><?php echo htmlspecialchars($product['brand'], ENT_COMPAT, 'UTF-8'); ?></h3>

    <p class="product-subtitle"><?php echo htmlspecialchars($product['description'], ENT_COMPAT, 'UTF-8'); ?></p>
    <p class="product-price">S$<?php echo number_format($product['base_price'], 2); ?></p>

    <div class="size-container">
      <h3 class="select-size">Select Size</h3>
      <a class="size-guide-link">Size Guide</a>
    </div>

    <div class="size-grid">
      <?php foreach ($sizes as $size): ?>
        <div class="size-option">
          <input type="radio" id="size-<?php echo htmlspecialchars($size['size']); ?>" name="size"
            value="<?php echo htmlspecialchars($size['size']); ?>">
          <label
            for="size-<?php echo htmlspecialchars($size['size']); ?>">US <?php echo htmlspecialchars($size['size'] + 0); //Internal equivalent to casting to float with (float)$num ?>
          </label>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="action-buttons">
      <input type="hidden" id="product-id" name="product-id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
      <button class="add-to-bag">Add to Cart</button>
      <!-- <button class="favourite">
        Favourite <span class="heart">&#9825;</span>
      </button> -->
    </div>
</div>
</div>
</section>
<?php include __DIR__ . "/../partials/reviews.php"; ?>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>