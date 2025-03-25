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