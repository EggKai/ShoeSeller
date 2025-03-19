<?php 
$title = "Welcome to Shoe Store";
include_once __DIR__ . '/../inc/header.php';
?>
<?php include __DIR__ . '/../partials/landing.php'; ?>
<div class="__content">
<?php
$carouselTitle = "New arrivals";
$carouselProducts = $recents; // Make sure $featuredProducts is defined in your controller
include __DIR__ . '/../partials/carousel.php'; 
$carouselTitle = "Best Sellers";
$carouselProducts = $bestsellers; // Make sure $featuredProducts is defined in your controller
include __DIR__ . '/../partials/carousel.php'; 
?>
</section>  
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
