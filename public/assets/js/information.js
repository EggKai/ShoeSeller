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

document.addEventListener("DOMContentLoaded", function () {

    registerEventListeners();
});

function registerEventListeners(){
    var title = document.getElementsByClassName("toggle_dropdown");

    if (title != null){
        for (var i = 0; i < title.length; i++) {
            var title = title[i];
            title.addEventListener("click", showContent(title));
        }
    }

}

function showContent(title){
    var titlecontent = title.children[1];
    if (titlecontent.style.visiblility == "hidden"){
        titlecontent.style.visiblility == "visible";
    }
    else if (titlecontent.style.visiblility == "visible"){
        titlecontent.style.visiblility == "hidden";
    }

}