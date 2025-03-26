function toggleDropdown(id, element) {
    var content = document.getElementById(id);
    var symbol = element.querySelector("span");

    // Check if the content is currently visible
    if (content.style.display === "block") {
        content.style.display = "none";
        symbol.textContent = "+";  // Change symbol to "+"
    } else {
        content.style.display = "block";
        symbol.textContent = "-";  // Change symbol to "-"
    }
}

document.addEventListener("DOMContentLoaded", function() {

    registerEventListeners();
});

function registerEventListeners() {
    var dropdown_Titles = document.querySelectorAll(".dropdown_title");

    dropdown_Titles.forEach(function (title) {
        title.addEventListener("click", function () {
            toggleContent(title);
        });
    });
}

function closeOtherDropdowns(currentTitle) {
    var dropdownTitles = document.querySelectorAll(".dropdown_title");
    dropdownTitles.forEach(function(title) {
        if (title !== currentTitle && title.classList.contains("active")) {
            var dropdown = title.parentElement;
            var content = dropdown.querySelector(".toggle_content");
            var icon = title.querySelector(".dropdown");
            // Remove classes to close the dropdown
            content.style.height = content.scrollHeight + 'px';  // Set current height explicitly
            requestAnimationFrame(() => {
                content.style.height = '0px';
                content.style.opacity = 0;
            });
            icon.innerHTML = "+";
            title.classList.remove("active");
            content.classList.remove("expanded");
        }
    });
}

function toggleContent(title) {
    closeOtherDropdowns(title);
    var dropdown = title.parentElement;
    var content = dropdown.querySelector(".toggle_content");
    var icon = title.querySelector(".dropdown");
    
    if (content.classList.contains("expanded")) {
        // Close: animate height from current to 0
        content.style.height = content.scrollHeight + 'px';  // Set current height explicitly
        requestAnimationFrame(() => {
            content.style.height = '0px';
            content.style.opacity = 0;
        });
        icon.innerHTML = "+";
        title.classList.remove("active");
        content.classList.remove("expanded");
    } else {
        // Open: animate height from 0 to scrollHeight
        content.classList.add("expanded");
        content.style.height = '0px';
        requestAnimationFrame(() => {
            content.style.height = content.scrollHeight + 'px';
            content.style.opacity = 1;
        });
        icon.innerHTML = "-";
        title.classList.add("active");

        // Once the transition is complete, reset the height to auto for responsiveness
        content.addEventListener('transitionend', function handler(e) {
            if (e.propertyName === 'height') {
                content.style.height = 'auto';
                content.removeEventListener('transitionend', handler);
            }
        });
    }
}

