document.addEventListener("DOMContentLoaded", function () {

    // Code to be executed when the DOM is ready (i.e. the document is
    // fully loaded):
    registerEventListeners(); // You need to write this function...
});

function registerEventListeners() {

    // The second demonstration shows how to get multiple elements with one
    // function call. In this example, we'll get ALL elements that have the
    // class attribute logo.
    var chart = document.getElementsByClassName("size-guide-link");

    // Again, check to make sure the returned object is not null, and also
    // check that the array is not empty (this would indicate there aren't any
    // elements with that class).
    if (chart !== null && chart.length > 0) {
        for (var i = 0; i < chart.length; i++) {
            var chart = chart[i];
            chart.addEventListener("click", showImg(chart));
        }
    }
    else {
        console.log("No chart found.");
    }

}

function showImg(chart) {
    /* Create portion to show image*/
    /*Div*/
    const imgdiv = document.createElement("div");
    imgdiv.setAttribute("class", "chartimg");
    /* Img */
    const img = document.createElement("img");
    img.setAttribute("class", "chartimg-content");

    chart.insertAdjacentElement("afterend", imgdiv);
    imgdiv.insertAdjacentElement("afterbegin", img);

    /*display img*/
    chart.onclick = function () {
        imgdiv.style.display = "block";
        img.src = '/public/assets/images/shoe-size-guide.png';
    }

    /* Close pop up image*/
    img.onclick = function () {
        imgdiv.style.display = "none";
    }
}