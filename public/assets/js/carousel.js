document.addEventListener("DOMContentLoaded", function() {
  // Select all carousel containers on the page
  const carousels = document.querySelectorAll('.carousel-container');
  
  // Set how many pixels to scroll each time
  const scrollAmount = 420; // adjust as necessary

  // Loop through each carousel container
  carousels.forEach(carousel => {
    // Get elements specific to this carousel
    const productList = carousel.querySelector('.product-list');
    const prevButton = carousel.querySelector('.prev-button');
    const nextButton = carousel.querySelector('.next-button');

    // Attach event listeners if elements exist
    if (productList && prevButton && nextButton) {
      prevButton.addEventListener('click', () => {
        productList.scrollBy({
          left: -scrollAmount,
          behavior: 'smooth'
        });
      });

      nextButton.addEventListener('click', () => {
        productList.scrollBy({
          left: scrollAmount,
          behavior: 'smooth'
        });
      });
    }
  });
});
