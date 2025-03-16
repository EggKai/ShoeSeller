<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="robots" content="noindex, nofollow">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($title) ? $title : "Shoe Store"; ?></title>
    <link rel="icon" type="image/x-icon" href="public/assets/images/favicon.ico">
    <link rel="stylesheet" href="public/assets/css/products.css">
    <link rel='stylesheet' href='public/assets/css/product-detail.css'>
    <link rel="stylesheet" href="public/assets/css/style.css">
    <link rel='stylesheet' href='public/assets/css/main.css'>
    <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/ScrollTrigger.min.js"></script>
    <?php if (isset($options)) { ?> <!-- allow for dynamic loading of content based on whats needed -->
        <?php if (in_array('cart', $options)) { ?>
            <link rel='stylesheet' href="public/assets/css/cart.css">
            <script defer src="public/assets/js/cart.js"></script>
        <?php } ?>
        <?php if (in_array('carousel', $options)) { ?>
            <link rel='stylesheet' href='public/assets/css/carousel.css'>
            <script defer src="public/assets/js/carousel.js"></script>
        <?php } ?>
        <?php if (in_array('landing', $options)) { ?>
            <script defer src="https://cdn.jsdelivr.net/npm/gsap@3.12.5/dist/gsap.min.js"></script>
            <script defer src="https://unpkg.com/lenis@1.1.14/dist/lenis.min.js"></script>
            <script defer src="public/assets/js/landing.js"></script>
        <?php } ?>
        <?php if (in_array('form', $options)) { ?>
            <link rel='stylesheet' href='public/assets/css/form.css'>
            <link rel="stylesheet" href="public/assets/css/alerts.css">
        <?php } ?>
        <?php if (in_array('form-carousel', $options)) { ?>
            <script defer src="public/assets/js/form-carousel.js"></script>
        <?php } ?>
        <?php if (in_array('checkout-form', $options)) { ?>
            <link rel='stylesheet' href='public/assets/css/checkout-form.css'>
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
        <?php } ?>
    <link rel="stylesheet" href="public\assets\css\footer.css">
</head>

<body>
    <header>
        <nav class="main-nav">
            <ul class="nav-left">
                <li>
                    <a href="index.php">
                        <img src="public/assets/images/logo.png" alt="Shoe Store Logo">
                    </a>
                </li>
                <li><a href="index.php?url=products/all">Products</a></li>
                <?php if (isset($_SESSION['user']) && $_SESSION['user']['user_type'] === 'admin') { ?>
                    <li><a href="index.php?url=admin/discount">View Users</a></li>
                    <li>
                        <a href="index.php?url=auth/logout">Logout</a>
                    </li>
                <?php } else { ?>
                    <li><a href="index.php?url=cart">Cart</a></li>
                    <?php if (isset($_SESSION['user'])) { ?>
                        <li>
                            <a href="index.php?url=auth/profile" class="user" aria-label="User Login">Profile</a>
                        </li>
                        <li>
                            <a href="index.php?url=auth/logout">Logout</a>
                        </li>
                    <?php } else { ?>
                        <li>
                            <a href="index.php?url=auth/login" class="user" aria-label="User Login">Login</a>
                        </li>
                    <?php } ?>
                <?php } ?>

                <li class="align-right">
                    <form action="index.php" method="get" class="search-form">
                        <input type="hidden" name="url" value="products/all">
                        <input type="text" name="query" placeholder="Search for shoes..." aria-label="Search">
                        <button type="submit" aria-label="Submit Search">
                            🔍
                        </button>
                    </form>
                </li>


            </ul>
        </nav>
    </header>

    <main>