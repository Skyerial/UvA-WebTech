var amountofCards = 0;
var data;

function specialChar(str) {
    return /^\w+( \w+)*$/.test(str);
}

// Get all data from api based on the search query
function getCardData(search){
    const container = document.getElementById('cardcontainerID');

    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "APICall/movieApiCall.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status == 200) {
            var movieDetails = xhttp.responseText;
            console.log(JSON.parse(movieDetails));
            data = JSON.parse(movieDetails);
            displayCards(data);
        }
    };

    xhttp.send('movieTitle=' + search);
}

// Execute all visual changes when searching a title.
function visualchanges() {
    var titlebox = document.getElementById("title");
    var titletext = document.getElementById("titletext");
    var contentbox = document.getElementById("contentID");

    if (titlebox.style.display === "none") {

    } else {
        titlebox.style.height = '0px'
        titletext.style.fontSize = '0px'
        contentbox.style.background = '#6c848c'
        var delayInMilliseconds = 1500;

        setTimeout(function() {
            titlebox.style.display = "none";

        }, delayInMilliseconds);
    }

    var footer = document.getElementById('footerID');
    footer.style.visibility = 'visible';
    var navLogo = document.getElementById('logoID');
    navLogo.style.visibility = 'visible';
    navLogo.style.opacity = 1;

    titlebox.style.paddingTop = '0';
    titlebox.style.paddingBottom = '0';
}


// Retrieves search query and executes correct functions.
function searchbutton() {
    var search = document.getElementById('textbar').value;
    if(!specialChar(search)) {
        document.getElementById('textbar').value = '';
        alert(
            "Please enter a valid search input containing only letter, numbers, and spaces."
        )
        return;
    }

    visualchanges();

    deleteCards();
    getCardData(search);
}

// Removes old cards if there are any present on the homepage.
function deleteCards() {
    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        card.remove();
    }
    amountofCards = 0;
}


// Changes card opacity so that the cards will appear smoothly
function changeOpacity(card) {
    setTimeout(() => {
        card.style.opacity = '1';
    }, 500);
}

// Creates a html div module for the streamingservice
function streamingdiv(service, servicelink) {

    var div = `<a href="${servicelink}" target="_blank" class="streamingservice">
                    <img src="streaming_img/${service}.png">
                </a>`;

    return div;
}

// Generates all the cards based on incoming data
function displayCards(data){
    removeLoadingAnimation();
    const container = document.getElementById('cardcontainerID');

    data.forEach(data => {
        //create streamingservice divs
        var divs = ``;
        if (data.netflix) {
            divs = divs + streamingdiv("netflix", data.netflix);
        }
        if (data.apple) {
            divs = divs + streamingdiv("apple", data.apple);
        }
        if (data.disney) {
            divs = divs + streamingdiv("disney", data.disney);
        }
        if (data.hbo) {
            divs = divs + streamingdiv("hbo", data.hbo);
        }
        if (data.hulu) {
            divs = divs + streamingdiv("hulu", data.hulu);
        }
        if (data.prime) {
            divs = divs + streamingdiv("prime", data.prime);
        }

        const content = `
            <div class="card" id="card${data.id}">
                <div class="imagebox">
                    <img class="poster" src="${data.moviePoster}"/>
                    <div class="streamingservicebox">
                        ${divs}
                    </div>
                </div>
                <h3>${data.movieTitle}</h3>
                <div class="hover-content">
                    <a href="javascript:void(0)" onclick="to_watch(${data.id}); return false;" class="cardbutton"><i class="fa-solid fa-clock"></i><span class="tooltiptext">Future Watching</span></a>
                    <a href="javascript:void(0)" onclick="cur_watching(${data.id}); return false;" class="cardbutton"><i class="fa-solid fa-eye"></i><span class="tooltiptext">Currently Watching</span></a>
                    <a href="javascript:void(0)" onclick="watched(${data.id}); return false;" class="cardbutton"><i class="fa-solid fa-eye-slash"></i><span class="tooltiptext">Finished Watching</span></a>
                </div>
            </div>
        `;

        // Append newyly created card element to the container
        container.innerHTML += content;
        amountofCards++;

    })

    // Change opacity for all cards so that they will appear smoothly
    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        changeOpacity(card);
    }

}

