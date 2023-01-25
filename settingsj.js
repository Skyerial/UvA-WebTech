function theme() {
    var element = document.body;
    //element.classList.toggle("light");
    // Start with light mode:
    element.classList.toggle("dark");
}

function dropdown() {
    var element = document.body;
    element.classList.toggle("show");
}

function new_region() {
    var element = document.body;
    // var div = document.getElementById("regions");
    // var selected = div.getElementsByTagName("label");
    // Show the regions
    //document.getElementById("selected-region").innerHTML = 
    element.classList.toggle("test");
}

function search() {
    var input = document.getElementById("search-input");
    // Set input string to uppercase.
    var lowcase_input = input.value.toLowerCase();
    // Get all region options.
    var div = document.getElementById("regions");
    var x = div.getElementsByTagName("label");

    // Loop through regions.
    for (var i = 0; i < x.length; i++) {
        region = x[i].textContent || x[i].innerText;
        region = region.toLowerCase();
        // Compare if a region contains any letter of the input.
        if (region.indexOf(lowcase_input) > -1) {
            // Display the region.
            x[i].style.display = "";
        } else {
            // Don't display region if it doesn't contain the input.
            x[i].style.display = "none";
        }
    }
}