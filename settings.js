// Show regions on click.
function dropdown() {
    var x = document.body;
    x.classList.toggle("show");
}

function input_user() {
    var x = document.body;
    x.classList.toggle("user_appear");
}

function input_pass() {
    var x = document.body;
    x.classList.toggle("pass_appear");
}

// Set new selected region and change region in database user.
function newRegion(region, email) {
    // Show the selected region.
    document.getElementById("selected-region").innerHTML = region;
    // Hide the dropdown after choosing a region.
    var x = document.body;
    x.classList.remove("show");

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