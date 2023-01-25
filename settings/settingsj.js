function dropdown() {
    var x = document.body;
    x.classList.toggle("show");
}

document.addEventListener("click", classList.remove("show"));

function new_region(region, email) {
    // Show the selected region.
    document.getElementById("selected-region").innerHTML = region;

    const body = JSON.stringify({
        region: region,
        email: email,
    });
    // Change the region per user in another file.
    const request = new Request('update_region.php', {
        method: 'POST', body: body
    });
    fetch(request);
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