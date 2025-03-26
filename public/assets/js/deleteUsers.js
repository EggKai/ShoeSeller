document.addEventListener('DOMContentLoaded', function() {
    const deleteButtons = document.querySelectorAll('.delete-user-btn');
    const modal = document.getElementById('delete-modal');
    const closeBtn = modal.querySelector('.close');
    const confirmDeleteBtn = document.getElementById('confirm-delete-btn');
    let currentUserId = null;

    // When a delete button is clicked, store the user id and show the modal.
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            currentUserId = this.dataset.userId;
            // Reset modal content to default prompt
            modal.querySelector('.modal-content').innerHTML = `
                <span class="close">&times;</span>
                <h3>Confirm Deletion</h3>
                <p>Please enter your admin password to confirm deletion:</p>
                <input type="password" id="admin-password" placeholder="Enter password">
                <button id="confirm-delete-btn">Confirm</button>
            `;
            attachModalListeners(); // reattach listeners for newly created elements
            modal.style.display = 'block';
        });
    });

    // Attach event listeners to modal close and confirm buttons.
    function attachModalListeners() {
        // Close button
        const newCloseBtn = modal.querySelector('.close');
        newCloseBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        // Confirm deletion button
        const newConfirmDeleteBtn = modal.querySelector('#confirm-delete-btn');
        newConfirmDeleteBtn.addEventListener('click', function() {
            const adminPassword = document.getElementById('admin-password').value;
            if (adminPassword.trim() === "") {
                updateModalMessage("Password is required.");
                return;
            }
            const csrfToken = document.getElementById('csrf_token').value;
            fetch('index.php?url=admin/deleteUser', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    user_id: currentUserId,
                    admin_password: adminPassword,
                    csrf_token: csrfToken
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    updateModalMessage("User deleted successfully.", true);
                    // Remove the row from the table after a short delay.
                    setTimeout(() => {
                        const row = document.getElementById('user-row-' + currentUserId);
                        if (row) row.remove();
                        modal.style.display = 'none';
                    }, 1500);
                } else {
                    updateModalMessage("Error: " + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                updateModalMessage("An error occurred while deleting the user.");
            });
        });
    }

    // Update modal content to show a message.
    function updateModalMessage(message, success = false) {
        const modalContent = modal.querySelector('.modal-content');
        modalContent.innerHTML = `
            <span class="close">&times;</span>
            <h3>${success ? "Success" : "Error"}</h3>
            <p>${message}</p>
            <button id="close-modal-btn">Close</button>
        `;
        // Attach close listener to new close elements.
        modal.querySelector('.close').addEventListener('click', function() {
            modal.style.display = 'none';
        });
        document.getElementById('close-modal-btn').addEventListener('click', function() {
            modal.style.display = 'none';
        });
    }

    // Close the modal if clicking outside the modal content.
    window.addEventListener('click', function(event) {
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
});
