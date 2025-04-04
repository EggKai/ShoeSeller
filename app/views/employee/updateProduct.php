<?php
$title = htmlspecialchars($product['name']);
$description = "Details of the product:".htmlspecialchars($product['name']);

include __DIR__ . '/../inc/header.php';
?>
<div class="__content">
<h1 class="visually-hidden">Update Product</h1>
<?php
include __DIR__ . '/../partials/alert.php';
?>
<div class="product-page ">
  <!-- LEFT COLUMN: Product Image -->
  <div class="product-image">
    <img src="/public/products/<?php echo htmlspecialchars($product['image_url']); ?>"
      alt="<?php echo htmlspecialchars($product['name']); ?>">
  </div>

  <!-- RIGHT COLUMN: Product Details -->
  <div class="product-info">
    <!-- Editable Form for Admin/Employee -->
    <form action="/employee/updateProduct&id=<?php echo htmlspecialchars($_GET['id']); ?>" method="POST"
      class="fill" id="authForm">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrf_token ?? ''); ?>">
      <div class="tab">
        <label for="name">Product Name:</label>
        <input type="text" id="name" name="name" value="<?php echo htmlspecialchars($product['name']); ?>">

        <label for="brand">Brand:</label>
        <input type="text" id="brand" name="brand" value="<?php echo htmlspecialchars($product['brand']); ?>">

        <label for="description">Description:</label>
        <textarea id="description" name="description" cols="30"
          rows="14"><?php echo htmlspecialchars($product['description']); ?></textarea>

        <label for="base_price">Price:</label>
        <input type="number" step="0.01" id="base_price" name="base_price"
          value="<?php echo htmlspecialchars($product['base_price']); ?>">
      </div>
      <div class="tab">
        <label for="thumbnail">Change Image:</label>
        <input type="file" id="thumbnail" name="thumbnail" accept="image/png, image/jpeg, image/avif, image/jpg">

        <div id="sizesContainer">
          <h3>Sizes & Stock:</h3>
          <?php if (isset($sizes) && !empty($sizes)): ?>
            <?php foreach ($sizes as $size): ?>
              <div class="size-row">
                <input type="number" name="sizes[]" placeholder="Size" oninput="this.className=''"
                  value="<?php echo htmlspecialchars($size['size']); ?>">
                <input type="number" name="stock[]" placeholder="Stock" min="0" oninput="this.className=''"
                  value="<?php echo htmlspecialchars($size['stock'] ?? ''); ?>">
                <button type="button" class="remove-size" onclick="removeSizeRow(this)">Delete</button>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <!-- Render a default row if no data is available -->
            <div class="size-row">
              <input type="number" name="sizes[]" placeholder="Size" oninput="this.className=''">
              <input type="number" name="stock[]" placeholder="Stock" min="0" oninput="this.className=''">
              <button type="button" class="remove-size" onclick="removeSizeRow(this)">Delete</button>
            </div>
          <?php endif; ?>
        </div>
        <button class="addButton" type="button" id="addSizeBtn">Add Size</button>
      </div>
      <div style="margin-top: 1rem;">
        <div style="float:right;">
          <button class="stepButton" type="button" id="prevBtn" onclick="nextPrev(-1)">
            Previous</button>
          <button class="stepButton" type="button" id="nextBtn" onclick="nextPrev(1)">Next</button>
        </div>
      </div>
      <div style="display:none;">
        <span class="step"></span>
        <span class="step"></span>
      </div>
  </div>
  <a href="/employee/handleListing&id=<?php echo htmlspecialchars($_GET['id']); ?>">
  <button class="floating-plus-button red" name="unlist" type="button">
      <?php echo ($product['unlisted'])?'🔓':'🔒';?>
    </button>
  </a>
  <button class="floating-plus-button" name="submit" type="submit">🛠️</button>
  </form>
</div>
  
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>