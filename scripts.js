var amountofCards = 0;
var data;

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
            console.log(JSON.parse(movieDetails));
            data = JSON.parse(movieDetails);
            displayCards(data);
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
        alert(
            "Please enter a valid search input containing only letter, numbers, and spaces."
        )
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

    // maybe this should be done with display none, as to not make invisible
    // buttons?...
    var footer = document.getElementById('footerID');
    footer.style.visibility = 'visible';
    var navLogo = document.getElementById('logoID');
    navLogo.style.visibility = 'visible';
    navLogo.style.opacity = 1;

    titlebox.style.paddingTop = '0';
    titlebox.style.paddingBottom = '0';

    deleteCards();
    // testCard();
    getCardData(search);
}

// removes old cards
function deleteCards() {
    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        card.remove();
    }
    amountofCards = 0;
}

// creates dummy cards
function testCard(){
    const container = document.getElementById('cardcontainerID');
    const moviePoster = "https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg";
    const movieTitle = "avatar avatar avatar avatar";

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
                    <div class="titlebox">
                        <h3 id="title${i}" value="${movieTitle}">${movieTitle}</h3>
                    </div>
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

    for (var j = 0; j < 35; j++) {
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
function streamingdiv(service, servicelink) {

    var div = `<a href="${servicelink}" target="_blank" class="streamingservice">
                    <img src="streaming_img/${service}.png">
                </a>`;

    return div;
}

// generates all the cards based on incoming data
function displayCards(data){
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

    //console.log(JSON.stringify(displayed));

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
