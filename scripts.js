var amountofCards = 0;

// This string check found on:
// https://codingbeautydev.com/blog/javascript-check-if-string-contains-only-letters-and-numbers/
// and:
// https://stackoverflow.com/questions/15472764/regular-expression-to-allow-spaces-between-words

function specialChar(str) {
    return /^\w+( \w+)*$/.test(str);
}

function getCardData(search){
    const container = document.getElementById('cardcontainerID');

    // START XMLHttpRequest
    var xhttp = new XMLHttpRequest();
    xhttp.open("POST", "APICall/movieApiCall.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status == 200) {
            var movieDetails = xhttp.responseText;
            displayCards(JSON.parse(movieDetails));
        }
    };

    xhttp.send('movieTitle=' + search);
    // END XMLHttpRequest
}

function animations() {
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

    titlebox.style.paddingTop = '0';
    titlebox.style.paddingBottom = '0';
}

function searchbutton() {
    var search = document.getElementById('textbar').value;
    if(!specialChar(search)) {
        document.getElementById('textbar').value = '';
        // need to make sure that it shows search input was not valid...
        return;
    }

    // handle animations
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

    titlebox.style.paddingTop = '0';
    titlebox.style.paddingBottom = '0';
    deleteCards();
    //testCard();
    getCardData(search);
}

// removes old cards
function deleteCards(amountofCards) {
    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        card.remove();
    }
}

// creates dummy cards
function testCard(){
    const container = document.getElementById('cardcontainerID');
    const moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
    const movieTitle = "Avatar";

    for(var i = 0; i < 10; i++) {

            const content = `
                <div class="card" id="card${i}">
                    <div class="imagebox">
                        <img class="poster" id="poster${i}" value="${moviePoster}" src="${moviePoster}"/>
                        <div class="streamingservicebox">
                            <div class="streamingservice">
                                <img src="streaming_img/netflix.png">
                            </div>
                            <div class="streamingservice">
                                <img src="streaming_img/netflix.png">
                            </div>
                            <div class="streamingservice">
                                <img src="streaming_img/netflix.png">
                            </div>
                        </div>
                    </div>
                    <h3 id="title${i}" value="${movieTitle}">${movieTitle}</h3>
                    <div class="hover-content">
                        <a href="javascript:void(0)" onclick="to_watch(${i}); return false;" class="cardbutton">Future</a>
                        <a href="javascript:void(0)" onclick="cur_watching(${i}); return false;" class="cardbutton">Current</a>
                        <a href="javascript:void(0)" onclick="watched(${i}); return false;" class="cardbutton">Watched</a>
                    </div>
                </div>
            `;

            // Append newly created card element to the container
            container.innerHTML += content;
            amountofCards++;
    }

    for (var j = 0; j < 10; j++) {
        var cardid = "card" + j;
        var card = document.getElementById(cardid);
        changeOpacity(card);
    }

}

//changes card opacity so that the cards will appear smoothly
function changeOpacity(card) {
    setTimeout(() => {
        card.style.opacity = '1';
    }, 500);
}

// creates a html div module for the streamingservice
function streamingdiv(service) {

    var div = `<div class="streamingservice">
                    <img src="streaming_img/${service}.png">
                </div>`;

    return div;
}

// generates all the cards based on incoming data
function displayCards(data){
    const container = document.getElementById('cardcontainerID');
    var information_available = true;

    data.forEach(data => {
        //create streamingservice divs
        var divs = ``;
        if (data.netflix == true) {
            divs = divs + streamingdiv("netflix");
        }
        if (data.apple == true) {
            divs = divs + streamingdiv("apple");
        }
        if (data.disney == true) {
            divs = divs + streamingdiv("disney");
        }
        if (data.hbo == true) {
            divs = divs + streamingdiv("hbo");
        }
        if (data.hulu == true) {
            divs = divs + streamingdiv("hulu");
        }
        if (data.prime == true) {
            divs = divs + streamingdiv("prime");
        }
        if (divs == `` || !data.moviePoster) {
            information_available = false;
        }

        if (information_available == true) {
            const content = `
                <div class="card" id="card${amountofCards}">
                    <div class="imagebox">
                        <img class="poster" id="poster${amountofCards}" value="${data.moviePoster}" src="${data.moviePoster}"/>
                        <div class="streamingservicebox">
                            ${divs}
                        </div>
                    </div>
                    <h3 id=title${amountofCards} value="${data.movieTitle}">${data.movieTitle}</h3>
                    <div class="hover-content">
                        <a href="javascript:void(0)" onclick="to_watch(${amountofCards}); return false;" class="cardbutton">Future</a>
                        <a href="javascript:void(0)" onclick="cur_watching(${amountofCards}); return false;" class="cardbutton">Current</a>
                        <a href="javascript:void(0)" onclick="watched(${amountofCards}); return false;" class="cardbutton">Watched</a>
                    </div>
                </div>
            `;

            // Append newyly created card element to the container
            container.innerHTML += content;
            amountofCards++;
        }
        information_available = true;
    })

    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        changeOpacity(card);
    }


}

function buildURL() {
    var startURL = 'https://streaming-availability.p.rapidapi.com/v2/search/title?title=';
    const countrySetting = '&country=';
    const typeSetting = '&type=';
    const languageSetting = '&output_language='
    var country = 'nl';
    var type = 'all';
    var language = 'en'
    // bit extra since it is already in searchButton, but that can be fixed later...
    var search = document.getElementById('textbar').value;
    document.getElementById('textbar').value = search;
    const searchURL = search.replace(/\s/g, '%20');

    return startURL + searchURL + countrySetting + country + typeSetting + type + languageSetting + language;
}
