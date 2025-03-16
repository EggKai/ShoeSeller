<?php 
$title = "Welcome to Shoe Store";
include_once __DIR__ . '/../inc/header.php';
?>
<?php include __DIR__ . '/../partials/landing.php'; ?>
<div class="__content">
<?php
$carouselTitle = "Featured Products";
$carouselProducts = $products; // Make sure $featuredProducts is defined in your controller
include __DIR__ . '/../partials/carousel.php'; 
$carouselTitle = "Best Sellers";
$carouselProducts = $products; // Make sure $featuredProducts is defined in your controller
include __DIR__ . '/../partials/carousel.php'; 
$carouselTitle = "New arrivals";
$carouselProducts = $products; // Make sure $featuredProducts is defined in your controller
include __DIR__ . '/../partials/carousel.php'; 
?>
</section>  
<?php include_once __DIR__ . '/../inc/footer.php'; ?>
