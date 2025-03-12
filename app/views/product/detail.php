<?php
$title = htmlspecialchars($product['name']);
include __DIR__ . '/../inc/header.php';
?>
<div class="product-page">

  <!-- LEFT COLUMN: Product Image -->
  <div class="product-image">
    <img 
      src="public/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
      alt="<?php echo htmlspecialchars($product['name']); ?>">
  </div>

  <!-- RIGHT COLUMN: Product Details -->
  <div class="product-info">
    <!-- Product Title -->
    <h1 class="product-name">
      <?php echo htmlspecialchars($product['name']); ?>
    </h1>
    <h3 class="product-brand">
    <?php echo htmlspecialchars($product['brand']); ?>
    </h3>
    <!-- Desc -->
    <p class="product-subtitle">
      <?php echo htmlspecialchars($product['description']); ?>
    </p>
    <!-- Price -->
    <p class="product-price">
      S$<?php echo number_format($product['base_price'], 2); ?>
    </p>
  <?php
  ?>
    <!-- Size Section -->
    <div class="size-container">
      <h3 class="select-size">Select Size</h3>
      <a href="#" class="size-guide-link">Size Guide</a>
    </div>
    
    <!-- Render Size Options -->
    <div class="size-grid">
    <?php foreach ($sizes as $size): ?>
        <div class="size-option">
            <input 
                type="radio" 
                id="size-<?php echo $size['size']; ?>" 
                name="size" 
                value="<?php echo htmlspecialchars( $size['size']); ?>" 
            >
            <label for="size-<?php echo $size['size']; ?>">
                <?php echo htmlspecialchars($size['size']); ?>
            </label>
        </div>
    <?php endforeach; ?>
</div>

    <!-- Buttons -->
    <div class="action-buttons">
    <button class="add-to-bag" data-product-id="<?php echo htmlspecialchars($_GET['id']); ?>">Add to Bag</button>
    <button class="favourite" data-product-id="<?php echo htmlspecialchars($_GET['id']);?>">
        Favourite <span class="heart">&#9825;</span>
      </button>
    </div>
  </div>

</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>