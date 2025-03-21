<?php
$title = "Welcome to Shoe Store";
include_once __DIR__ . '/../inc/header.php';
?>
<?php include __DIR__ . '/../partials/landing.php'; ?>
<div class="__content">
    <?php
    $carouselTitle = "Best Sellers";
    $carouselProducts = $bestsellers; // Make sure $featuredProducts is defined in your controller
    include __DIR__ . '/../partials/carousel.php';
    $carouselTitle = "New arrivals";
    $carouselProducts = $recents; // Make sure $featuredProducts is defined in your controller
    include __DIR__ . '/../partials/carousel.php';
    ?>
    <section class="cta">
    <div class="cta-content">
      <h1>Shop now!</h1>
      <p>Don't miss out on this amazing opportunity to be part of our community.</p>
      <a href="index.php?url=products/all" class="button">Get Started</a>
    </div>
  </section>
</div>
<?php include_once __DIR__ . '/../inc/footer.php'; ?>