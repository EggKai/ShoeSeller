<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <?php if (str_contains($_ENV['DOMAIN'], 'localhost')) {
        echo '<base href="/shoeseller/">';
    } ?>
    <!-- <meta name="robots" content="noindex, nofollow"> -->
    <meta name="description" content=<?php echo isset($description) ? $description : "Shoe Store Page"; ?>>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : "Shoe Store"; ?></title>
    <link rel="icon" type="image/x-icon" href="public/assets/images/favicon.ico">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <link rel="stylesheet" href="public/assets/css/main.css">
    <link rel="stylesheet" href="public/assets/css/products.css">
    <link rel="stylesheet" href="public/assets/css/product-detail.css">
    <link rel="stylesheet" href="public/assets/css/additional-styles.css">
    <link rel="stylesheet" href="public/assets/css/hamburger-nav.css">
    <script defer src="public/assets/js/hamburger-nav.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <?php if (isset($options)) { ?> <!-- dynamic loading of assets -->
        <?php if (in_array('cart', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/cart.css">
            <script defer src="public/assets/js/cart.js"></script>
        <?php } ?>
        <?php if (in_array('carousel', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/carousel.css">
            <script defer src="public/assets/js/carousel.js"></script>
        <?php } ?>
        <?php if (in_array('landing', $options)) { ?>
            <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
            <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"></script>
            <script defer src="public/assets/js/landing.js"></script>
        <?php } ?>
        <?php if (in_array('form', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/form.css">
            <link rel="stylesheet" href="public/assets/css/alerts.css">
        <?php } ?>
        <?php if (in_array('form-carousel', $options)) { ?>
            <script defer src="public/assets/js/form-carousel.js"></script>
        <?php } ?>
        <?php if (in_array('form-carousel-forked', $options)) { ?>
            <script defer src="public/assets/js/form-carousel-forked.min.js"></script>
        <?php } ?>
        <?php if (in_array('profile', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/profile.css">
        <?php } ?>
        <?php if (in_array('checkout-form', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/checkout-form.css">
        <?php } ?>
        <?php if (in_array('addCart', $options)) { ?>
            <script defer src="public/assets/js/add-to-cart.js"></script>
        <?php } ?>
        <?php if (in_array('sizes-list', $options)) { ?>
            <script defer src="public/assets/js/sizes-list.js"></script>
        <?php } ?>
        <?php if (in_array('floating-button', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/floating-button.css">
        <?php } ?>
        <?php if (in_array('dashboard', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/dashboard.css">
        <?php } ?>
        <?php if (in_array('view-users', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/view-users.css">
            <script defer src="public/assets/js/deleteUsers.js"></script>
        <?php } ?>
        <?php if (in_array('aboutus', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/aboutus.css">
        <?php } ?>
        <?php if (in_array('contactus', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/information.css">
        <?php } ?>
        <?php if (in_array('information', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/information.css">
            <script defer src="public/assets/js/information.js"></script>
        <?php } ?>
        <?php if (in_array('review', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/review.css">
        <?php } ?>
        <?php if (in_array('locations', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/locations.css">
            <script defer src="public/assets/js/locations.js"></script>
        <?php } ?>
        <?php if (in_array('view-logs', $options)) { ?>
            <link rel="stylesheet" href="public/assets/css/logs.css">
            <script defer src="public/assets/js/logs.js"></script>
        <?php } ?>
    <?php } ?>
    <link rel="stylesheet" href="public/assets/css/footer.css">
</head>

<body>
    <header role="banner">
        <nav class="main-nav" aria-label="Main navigation">
            <ul class="nav-left desktop-nav">
                <li>
                    <a href="index.php">
                        <img src="public/assets/images/logo.png" alt="Shoe Store Logo">
                    </a>
                </li>
                <li><a href="index.php?url=products/all">Products</a></li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>
                    <li><a href="index.php?url=admin/dashboard">Dashboard</a></li>
                    <li><a href="index.php?url=auth/logout">Logout</a></li>
                <?php } else { ?>
                    <?php if (isset($_SESSION['user'])) { ?>
                        <li><a href="index.php?url=auth/logout">Logout</a></li>
                    <?php } ?>
                <?php } ?>
                <li class="align-right">
                    <form action="index.php" method="get" class="search-form">
                        <input type="hidden" name="url" value="products/all">
                        <input type="text" name="query" placeholder="Search for shoes..." aria-label="Search for shoes"
                            value="<?php echo filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS); ?>">
                        <button type="submit" aria-label="Submit Search">
                            <i class="search-icon fas fa-search"></i>
                        </button>
                        <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee'])) { ?>
                            <a href="employee/tickets" class="icon-link" aria-label="View User Tickets">
                                <i class="fa-solid fa-ticket"></i>
                            </a>
                        <?php } else { ?>
                            <a href="cart" class="icon-link" aria-label="View Cart">
                                <i class="fa-solid fa-cart-shopping"></i>
                            </a>
                        <?php } ?>
                        <a href="<?php
                        switch ($_SESSION['user']['user_type'] ?? null) {
                            case 'admin':
                                echo "admin/users";
                                break;
                            case 'employee':
                            case 'user':
                                echo "auth/profile";
                                break;
                            default:
                                echo "auth/login";
                        } ?>" class="icon-link" aria-label="User Account">
                            <i class="fa-solid fa-user"></i>
                        </a>
                    </form>
                </li>
            </ul>

            <!-- Mobile Navigation: shown on small screens -->
            <div class="mobile-nav">
                <button class="mobile-menu-toggle" aria-label="Toggle Menu">
                    <i class="fa-solid fa-bars"></i>
                </button>
                <ul class="nav-left mobile-menu-items">
                    <li>
                        <a href="index.php">
                            Home
                        </a>
                    </li>
                    <li><a href="index.php?url=products/all">Products</a></li>
                    <?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>
                        <li><a href="index.php?url=admin/dashboard">Dashboard</a></li>
                    <?php } ?>
                    <li>
                        <a href="<?php
                        switch ($_SESSION['user']['user_type'] ?? null) {
                            case 'admin':
                                echo "admin/users";
                                break;
                            case 'employee':
                            case 'user':
                                echo "auth/profile";
                                break;
                            default:
                                echo "auth/login";
                        } ?>"><i class="fa-solid fa-user"></i> Account</a>
                    </li>
                    <?php if (isset($_SESSION['user']) && in_array($_SESSION['user']['user_type'], ['admin', 'employee'])) { ?>
                        <li> <!-- Include Tickets/Cart in Mobile Menu -->
                            <a href="employee/tickets" aria-label="View User Tickets">
                                <i class="fa-solid fa-ticket"></i> Tickets
                            </a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="index.php?url=cart" aria-label="View Cart">
                                <i class="fa-solid fa-cart-shopping"></i> Cart
                            </a>
                        </li>
                    <?php } ?>
                    <li> <!-- Include Search in Mobile Menu -->
                        <form action="index.php" method="get" class="mobile-search-form">
                            <input type="hidden" name="url" value="products/all">
                            <input type="text" name="query" placeholder="Search for shoes..."
                                aria-label="Search for shoes"
                                value="<?php echo filter_input(INPUT_GET, 'query', FILTER_SANITIZE_SPECIAL_CHARS); ?>">
                            <button type="submit" aria-label="Submit Search">
                                <i class="fas fa-search"></i>
                            </button>
                        </form>
                    </li>
                    <?php if (isset($_SESSION['user'])) { ?>
                            <li><a href="index.php?url=auth/logout">Logout</a></li>
                        <?php } else { ?>
                            <li><a href="index.php?url=auth/login">Login</a></li>
                        <?php } ?>
                </ul>
            </div>
        </nav>
        </nav>
    </header>

    <main id="main-content">