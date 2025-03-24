function updateMap(latitude, longitude) {
    const iframe = document.getElementById('mapFrame');
    const url = `https://www.google.com/maps?q=${latitude},${longitude}&output=embed`;
    iframe.src = url;
}

function filterLocations() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const locationItems = document.querySelectorAll('.location-item');

    locationItems.forEach(item => {
        const text = item.innerText.toLowerCase();
        if (text.includes(searchTerm)) {
            item.style.display = 'block';
        } else {
            item.style.display = 'none';
        }
    });
}

function toggleAddLocationForm() {
    const addFormCon = document.querySelector('.add-form-container');
    if (addFormCon.style.display === "block") {
        addFormCon.style.display = "none";
    }
    else {
        addFormCon.style.display = "block";
    }
}

// Toggling on or off the Loading Animation
function toggleLoader() {
    const loader = document.getElementById('loader');
    if (loader.style.display === "block") {
        loader.style.display = "none";
    }
    else {
        loader.style.display = "block";
    }
}

// Helper function for validating lat long
function validateCoordinates(latitude, longitude) {
    const lat = parseFloat(latitude);
    const lon = parseFloat(longitude);

    if (isNaN(lat) || isNaN(lon)) return false;

    // Check if the values are within valid ranges
    return lat >= -90 && lat <= 90 && lon >= -180 && lon <= 180;
}

function validateForm(form) {
    const formData = new FormData(form);

    // Validation
    const latitude = formData.get('latitude');
    const longitude = formData.get('longitude');

    if (!validateCoordinates(latitude, longitude)) {
        alert('Invalid latitude or longitude. Latitude must be between -90 and 90. Longitude must be between -180 and 180.');
        toggleLoader();
        return 0;
    }

    const inputs = form.querySelectorAll('input[type="text"], input[type="hidden"]');
    const values = {};

    for (const input of inputs) {
        if (!input.value.trim()) {
            alert('Please fill out all fields.');
            return 0;
        }
        values[input.name] = input.value.trim();
    }
    return 1;
    // Validation End
}

function addLocation(event) {
    toggleLoader();
    event.preventDefault();  // Prevent form submission

    const form = event.target;
    const formData = new FormData(form);

    if (!validateForm(form)) return; // Validation

    // Send the form data via a POST request
    fetch('index.php?url=information/doAddLocation', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        toggleLoader();
        if (data.success) {
            alert("Location added successfully!");

            const [id, name, address, country, zip_code, phone, latitude, longitude] = data.data;
            const locationList = document.querySelector('.location-list');

            // Create a new location-item element
            const locationItem = document.createElement('div');
            locationItem.className = 'location-item';
            locationItem.id = `location-${id}`;
            locationItem.setAttribute('onclick', `updateMap(${latitude}, ${longitude})`);

            locationItem.innerHTML = `
                <div class="item">
                    <div hidden class="latitude">${latitude}</div>
                    <div hidden class="longitude">${longitude}</div>

                    <div class="name"><strong>${name}</strong></div>
                    <div class="details">
                        <div class="address">${address}</div>
                        <div class="country">${country}, <span class="zip_code">${zip_code}</span></div>
                        <div class="phone">${phone}</div>
                    </div>
                    <div class="actions-container">
                        <button onclick="updateLocation(${id})" class="update-location-button">Edit</button>
                        <button onclick="removeLocation(${id})" class="delete-location-button">Delete</button>
                    </div>
                </div>

                <div class="edit-form" id="edit-${id}" style="display: none;">
                    <form onsubmit="saveLocation(event, ${id})">
                        <input type="hidden" name="id" value="${id}" required>
                        <input type="text" name="name" value="${name}" required>
                        <input type="text" name="address" value="${address}" required>
                        <input type="text" name="country" value="${country}" required>
                        <input type="text" name="zip_code" value="${zip_code}" required>
                        <input type="text" name="phone" value="${phone}" required>
                        <input type="text" name="latitude" value="${latitude}" required>
                        <input type="text" name="longitude" value="${longitude}" required>
                        <input type="hidden" name="csrf_token" value="${form.querySelector('[name=csrf_token]').value}">
                        <input type="hidden" name="submit" value="1">
                        <div class="buttons">
                            <button type="submit">Save</button>
                            <button type="button" onclick="discardChanges(event, ${id})">Discard</button>
                        </div>
                    </form>
                </div>
            `;

            // Append the new item to the location list
            locationList.appendChild(locationItem);

            // Clear the add location form
            form.reset();
            toggleAddLocationForm();


        } else {
            alert(data.message);
        }
    })
    .catch(error => console.error('Error:', error));
}

function discardAddLocationForm() {
    if (confirm('Are you sure you want to discard this entry?')) {
        const addForm = document.querySelector('.new-location-item');
        toggleAddLocationForm();
        addForm.reset();
    }
}

// Function to toggle edit mode
function updateLocation(id) {
    const editForm = document.getElementById(`edit-${id}`);
    const locationItem = document.getElementById(`location-${id}`);

    if (editForm && locationItem) {
        // Show the edit form and hide the item view
        editForm.style.display = 'block';
        locationItem.querySelector('.item').style.display = 'none';

        // Disable map update on click for this item while editing
        locationItem.setAttribute('data-disabled', 'true');
        locationItem.onclick = null;
    }
}

// Function to handle the saving of the edited location
function saveLocation(event, locationId) {
    toggleLoader();
    event.preventDefault();  // Prevent form submission
    
    const form = event.target;
    const formData = new FormData(form);

    if (!validateForm(form)) return; // Validation

    // Send the form data via a POST request
    fetch('index.php?url=information/doUpdateLocation', {
        method: 'POST',
        body: formData,
    })
    .then(response => response.json())
    .then(data => {
        toggleLoader();
        if (data.success) {
            alert("Location updated successfully!");
        
        const [id, name, address, country, zip_code, phone, latitude, longitude] = data.data;
        const locationItem = document.getElementById(`location-${id}`);
        const itemView = locationItem.querySelector('.item');
        if (!itemView) {
            console.error("Item view not found. Check your HTML structure.");
            return;
        }
        const editForm = document.getElementById(`edit-${id}`);

        // Update the location data display
        itemView.querySelector('.name').innerText = name;
        itemView.querySelector('.address').innerText = address;
        itemView.querySelector('.country').innerText = country;
        itemView.querySelector('.country').innerHTML += `<span class='zip_code'>, ` + zip_code + `</span>`;
        itemView.querySelector('.phone').innerText = phone;

        // Update the latitude and longitude stored in hidden elements
        itemView.querySelector('.latitude').innerText = latitude;
        itemView.querySelector('.longitude').innerText = longitude;

        // Update the onClick function for updating the map
        locationItem.setAttribute('onclick', `updateMap(${latitude}, ${longitude})`);

        // Toggle visibility
        editForm.style.display = "none";
        itemView.style.display = "block";
        } else {
            alert(data.message);
        }
    });
}

// Function to confirm discarding changes
function discardChanges(event, id) {
    event.preventDefault();
    const editForm = document.getElementById(`edit-${id}`);
    const locationItem = document.getElementById(`location-${id}`);

    if (confirm('Are you sure you want to discard the changes?')) {
        // Revert to the original location item (no changes made)
        editForm.style.display = "none";
        locationItem.querySelector('.item').style.display = 'block';

        // Re-enable map update on click
        locationItem.setAttribute('data-disabled', 'false');
        const latitude = locationItem.querySelector('.latitude').innerText;
        const longitude = locationItem.querySelector('.longitude').innerText;
        locationItem.setAttribute('onclick', `updateMap(${latitude}, ${longitude})`);
    }
}

// Function to handle location deletion
function removeLocation(locationId) {
    toggleLoader();
    var csrfToken = document.querySelector('input[name="csrf"]').value;

    if (confirm('Are you sure you want to delete this location?')) {
        const formData = new FormData();
        formData.append('id', locationId);
        formData.append('csrf_token', csrfToken);
        formData.append('submit', 'submit');

        // Send a POST request to delete the location
        fetch('index.php?url=information/doRemoveLocation', {
            method: 'POST',
            body: formData,
        })
        .then(response => response.json())
        .then(data => {
            toggleLoader();
            if (data.success) {
                document.getElementById(`location-${locationId}`).remove();
                alert("Location deleted successfully!");
            } else {
                alert(data.message);
            }
        });
    }
}