<?php
// Ensure that $carouselProducts is defined. If not, default to an empty array.
if (!isset($carouselProducts)) {
    $carouselProducts = [];
}

// Optional label for the carousel
$label = isset($carouselTitle) ? $carouselTitle : 'Products';
?>
<div class="carousel-container">
    <?php if ($label): ?>
        <h2><?php echo htmlspecialchars($label); ?></h2>
    <?php endif; ?>
    
    <!-- Prev Button -->
    <button class="carousel-button prev-button" aria-label="Previous">&#10096;</button>
    
    <!-- Scrollable Track -->
    <ul class="product-list">
        <?php if (count($carouselProducts) > 0): ?>
            <?php foreach ($carouselProducts as $product): ?>
                <li class="product-item">
                    <a href="/products/detail&id=<?php echo $product['id']; ?>">
                        <img src="/public/products/<?php echo htmlspecialchars($product['image_url']); ?>" 
                             alt="<?php echo htmlspecialchars($product['name']); ?>" >
                        <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                        <p><?php echo htmlspecialchars($product['brand']); ?></p>
                        <p>Price: $S<?php echo number_format($product['base_price'], 2); ?></p>
                    </a>
                </li>
            <?php endforeach; ?>
        <?php else: ?>
            <li><p>No products available at the moment.</p></li>
        <?php endif; ?>
    </ul>
    
    <!-- Next Button -->
    <button type="button" class="carousel-button next-button" aria-label="Next">&#10097;</button>
</div>
