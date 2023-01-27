// Show regions on click.
function dropdown() {
    var x = document.body;
    x.classList.toggle("show");
}

// This doesn't work, click anywhere and close dropdown again.
document.addEventListener("click", classList.remove("show"));

// Set new selected region and change region in database user.
function new_region(region, email) {
    // Show the selected region.
    document.getElementById("selected-region").innerHTML = region;

    // Send the data to update_region.php:
    fetch('update_region.php', {
        method: 'POST',
        credentials: 'same-origin',
        body: JSON.stringify({
            region: region,
            email: email
        })
    }).then(response => {
        if (response.headers.get('Content-Type') !== 'application/json') {
            throw new Error('Unexpected Content-Type');
        } else if (response.status === 200) {
            alert("Region updated successfully!");
        } else {
            alert("Error occurred while updating region!");
        }
        return response.json();
    }).catch(error => {
        console.error('Error:', error);
    });
}


// Search bar region.
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