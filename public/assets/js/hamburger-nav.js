document.addEventListener('DOMContentLoaded', function() {
    const toggleButton = document.querySelector('.mobile-menu-toggle');
    const mobileMenu = document.querySelector('.mobile-menu-items');
  
    if (toggleButton) {
      toggleButton.addEventListener('click', function() {
        mobileMenu.classList.toggle('open');
        if (mobileMenu.classList.contains('open')) {
          toggleButton.innerHTML = '<i class="fa-solid fa-times"></i>';
        } else {
          toggleButton.innerHTML = '<i class="fa-solid fa-bars"></i>';
        }
      });
    }
  });
  