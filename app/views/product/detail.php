<?php
$title = htmlspecialchars($product['name']);
include __DIR__ . '/../inc/header.php';
?>
<div class="product-page">

  <!-- LEFT COLUMN: Product Image -->
  <div class="product-image">
    <img src="public/products/<?php echo htmlspecialchars($product['image_url']); ?>"
      alt="<?php echo htmlspecialchars($product['name']); ?>">
  </div>

  <!-- RIGHT COLUMN: Product Details -->
  <div class="product-info">
    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee'])): ?>
      <!-- Editable Form for Admin/Employee -->
      <form action="index.php?url=admin/updateProduct&id=<?php echo htmlspecialchars($_GET['id']); ?>" method="POST">
        <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">

        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">

        <label for="description">Description:</label>
        <textarea id="description" name="description" cols="30"
          rows="5"><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="base_price">Price:</label>
        <input type="number" step="0.01" id="base_price" name="base_price"
          value="<?php echo htmlspecialchars($product['base_price']); ?>">

        <!-- Optionally include a file input for a new image -->
        <label for="thumbnail">Change Image:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/png, image/jpeg, image/avif, image/jpg">

        <!-- Size Options (read-only, but you might allow editing sizes if needed) -->
        <div class="size-grid">
          <?php foreach ($sizes as $size): ?>
            <div class="size-option">
              <input type="radio" id="size-<?php echo htmlspecialchars($size['size']); ?>" name="size"
                value="<?php echo htmlspecialchars($size['size']); ?>">
              <label
                for="size-<?php echo htmlspecialchars($size['size']); ?>"><?php echo htmlspecialchars($size['size']); ?></label>
            </div>
          <?php endforeach; ?>
        </div>
        <button class="floating-plus-button" type="submit">🛠️</button>
      </form>
    <?php else: ?>
      <!-- Regular Display for Customers -->
      <h1 class="product-name"><?php echo htmlspecialchars($product['name']); ?></h1>
      <h3 class="product-brand"><?php echo htmlspecialchars($product['brand']); ?></h3>
      <p class="product-subtitle"><?php echo htmlspecialchars($product['description']); ?></p>
      <p class="product-price">S$<?php echo number_format($product['base_price'], 2); ?></p>

      <div class="size-container">
        <h3 class="select-size">Select Size</h3>
        <a href="#" class="size-guide-link">Size Guide</a>
      </div>

      <div class="size-grid">
        <?php foreach ($sizes as $size): ?>
          <div class="size-option">
            <input type="radio" id="size-<?php echo htmlspecialchars($size['size']); ?>" name="size"
              value="<?php echo htmlspecialchars($size['size']); ?>">
            <label
              for="size-<?php echo htmlspecialchars($size['size']); ?>"><?php echo htmlspecialchars($size['size']); ?></label>
          </div>
        <?php endforeach; ?>
      </div>

      <div class="action-buttons">
      <input type="hidden" id="product-id" name="product-id" value="<?php echo htmlspecialchars($_GET['id']); ?>">
        <button class="add-to-bag">Add to Bag</button>
        <button class="favourite">
          Favourite <span class="heart">&#9825;</span>
        </button>
      </div>
    <?php endif; ?>

  </div>
</div>

<?php include_once __DIR__ . '/../inc/footer.php'; ?>