document.addEventListener('DOMContentLoaded', function() {
    const addSizeBtn = document.getElementById('addSizeBtn');
    const sizesContainer = document.getElementById('sizesContainer');
  
    // Create a new size row element
    function createSizeRow() {
      // Create container div
      const div = document.createElement('div');
      div.className = 'size-row';
      
      // Create input for size
      const sizeInput = document.createElement('input');
      sizeInput.type = 'text';
      sizeInput.name = 'sizes[]';
      sizeInput.placeholder = 'Size';
      sizeInput.setAttribute('oninput', "this.className=''");
      
      // Create input for stock
      const stockInput = document.createElement('input');
      stockInput.type = 'number';
      stockInput.name = 'stock[]';
      stockInput.placeholder = 'Stock';
      stockInput.min = '0';
      stockInput.setAttribute('oninput', "this.className=''");
      
      // Create Remove button
      const removeBtn = document.createElement('button');
      removeBtn.type = 'button';
      removeBtn.className = 'remove-size';
      removeBtn.textContent = 'Remove';
      removeBtn.addEventListener('click', function() {
        removeSizeRow(removeBtn);
      });
      
      // Append elements to the div
      div.appendChild(sizeInput);
      div.appendChild(stockInput);
      div.appendChild(removeBtn);
      
      return div;
    }
  
    // Add a new size row when clicking the "Add Size" button
    addSizeBtn.addEventListener('click', function() {
      const newRow = createSizeRow();
      sizesContainer.appendChild(newRow);
    });
  });
  
  // Remove the size row (passed as the button's parent)
  function removeSizeRow(btn) {
    const row = btn.parentNode;
    row.parentNode.removeChild(row);
  }
  