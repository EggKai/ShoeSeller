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

function toggleContent(titles){
    var dropdown = titles.parentElement;
    var content = dropdown.querySelector(".toggle_content");
    var icon = titles.querySelector(".dropdown");
    
    if(content){
        if (content.style.display === "none" || content.style.display === "") {
            content.style.display = "block";
            icon.innerHTML = "-"; 
        } else {
            content.style.display = "none";
            icon.innerHTML = "+";
        }
    }
}