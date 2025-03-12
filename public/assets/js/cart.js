document.addEventListener('DOMContentLoaded', function() {
  // Select all quantity control containers
  const quantityControls = document.querySelectorAll('.quantity-controls');
  
  quantityControls.forEach(control => {
    // Get references to the minus button, plus button, and input
    const minusBtn = control.querySelector('.qty-minus');
    const plusBtn = control.querySelector('.qty-plus');
    const input = control.querySelector('.qty-input');
    
    // Decrease quantity
    minusBtn.addEventListener('click', function(e) {
      e.preventDefault();
      let currentVal = parseInt(input.value, 10);
      if (currentVal > 1) {
        input.value = currentVal - 1;
      }
    });
    
    // Increase quantity
    plusBtn.addEventListener('click', function(e) {
      e.preventDefault();
      let currentVal = parseInt(input.value, 10);
      input.value = currentVal + 1;
    });
  });
});


