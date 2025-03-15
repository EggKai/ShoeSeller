<?php
require_once __DIR__ . "/Product.php";

class Cart
{
    public static function getCurrentCart()
    {
        $cart = [];
        if (isset($_COOKIE['cart'])) {
            $decoded = json_decode(urldecode($_COOKIE['cart']), true);
            if (is_array($decoded)) {
                $cart = $decoded;
            }
        }
        return $cart;
    }
    /**
     * Update the cart cookie with the provided cart array.
     *
     * @param array $cart The cart array to be saved.
     */
    public static function updateCart(array $cart)
    {
        // Set cookie for 7 days
        setcookie('cart', urlencode(json_encode($cart)), time() + (7 * 24 * 60 * 60), "/");
    }
    /**
     * Decrease the quantity of a cart item. Remove item if quantity becomes 0.
     *
     * @param string $productId The product ID.
     * @param string $size      The product size.
     * @param string $number    The number added to quantity.
     * @return array            The updated cart.
     */
    public static function updateCartitemQuantity($productId, $size, $number)
    {
        $cart = Cart::getCurrentCart();
        $found = false;
        // Loop through items to find a match.
        foreach ($cart as $i => $item) {
            if ($item[0] === $productId && $item[1] === $size) {
                if ($number > 0) {
                    $cart[$i][2] = $cart[$i][2] + $number;  // Increase quantity
                } else {
                    if ($item[2] > 1) {
                        $cart[$i][2] = $cart[$i][2] + $number;  // Decrease quantity
                    } else {
                        // Remove the item if quantity is 1
                        unset($cart[$i]);
                    }
                }
                $found = true;
                break;
            }
        }
        // If not found, add new item with quantity 1.
        if (!$found && $number > 0) {
            $cart[] = [$productId, $size, 1];
        }
        Cart::updateCart($cart);
        return $cart;
    }
    public static function fullCartDetails($cart) {
        $productModel = new Product();
        $cartItems = [];
        // Loop through each cart item from the cookie
        foreach ($cart as $item) {
            // Retrieve the product details using its ID
            $product = $productModel->getProductById(id: $item[0]);
            if ($product) {
                $category = $productModel->findById('categories', $product['category_id']);
                $product['quantity'] = $item[2];
                // Add the selected size and quantity to the product data
                $product['size'] = $item[1];
                $product['category'] = $category;
                // Optionally, calculate the total for this product (if price is stored per unit)
                $product['item_total'] = $product['base_price'] * $product['quantity'];
                $cartItems[] = $product;
            }
        }
        return $cartItems;
    }
    public static function deleteCart() {
        setcookie('cart', '', time() - 3600, "/"); // Remove the 'cart' cookie by setting its expiration time to the past
    }
}