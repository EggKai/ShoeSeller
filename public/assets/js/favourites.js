document.addEventListener('DOMContentLoaded', function() {
    const favouriteButtons = document.querySelectorAll('.favourite');
  
    favouriteButtons.forEach(button => {
      button.addEventListener('click', function(e) {
        e.preventDefault();
        
        // Retrieve product ID from data attribute
        const productId = this.dataset.productId;
        
        // Retrieve current favourites from cookie
        let favourites = [];
        const favCookie = document.cookie.split('; ').find(row => row.startsWith('favourites='));
        if (favCookie) {
          try {
            favourites = JSON.parse(decodeURIComponent(favCookie.split('=')[1]));
          } catch (err) {
            favourites = [];
          }
        }
        
        // Check if this product is already in the favourites list.
        const index = favourites.indexOf(productId);
        if (index > -1) {
          // Remove product from favourites.
          favourites.splice(index, 1);
          this.innerHTML = 'Favourite <span class="heart">&#9825;</span>';
        } else {
          // Add product to favourites.
          favourites.push(productId);
          this.innerHTML = 'Favourited <span class="heart">&#9829;</span>';
        }
        
        // Update the favourites cookie (expires in 30 days)
        document.cookie = "favourites=" + encodeURIComponent(JSON.stringify(favourites)) + "; path=/; max-age=" + (30 * 24 * 60 * 60);
        
        alert('Favourites list updated!');
      });
    });
  });
  