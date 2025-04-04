document.addEventListener('DOMContentLoaded', function() {
  const addToBagButtons = document.querySelectorAll('.add-to-bag');

  addToBagButtons.forEach(button => {
    button.addEventListener('click', function(e) {
      e.preventDefault();
      
      // Retrieve product ID from the hidden input field
      const productId = document.querySelector('input[name="product-id"]').value;
      
      // Get the selected size (if none selected, alert the user)
      const selectedSize = document.querySelector('input[name="size"]:checked')?.value;
      if (!selectedSize) {
        alert('Please select a size.');
        return;
      }

      // Retrieve current cart from cookie
      let cart = [];
      const cartCookie = document.cookie.split('; ').find(row => row.startsWith('cart='));
      if (cartCookie) {
        try {
          cart = JSON.parse(decodeURIComponent(cartCookie.split('=')[1]));
        } catch (err) {
          cart = [];
        }
      }

      // Each item is stored as an array: [productId, size, quantity]
      // Check if an item with the same productId and size already exists.
      const existingItemIndex = cart.findIndex(item => item[0] === productId && item[1] === selectedSize);
      if (existingItemIndex > -1) {
        // Increase quantity if it exists.
        cart[existingItemIndex][2] += 1;
      } else {
        // Otherwise, add new item with quantity 1.
        cart.push([productId, selectedSize, 1]);
      }

      // Update the cart cookie (set to expire in 7 days)
      document.cookie = "cart=" + encodeURIComponent(JSON.stringify(cart)) + "; path=/; max-age=" + (7 * 24 * 60 * 60);
      
      alert('Item added to cart!');
    });
  });
});
