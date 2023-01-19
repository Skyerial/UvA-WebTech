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
    xhttp.open("POST", "APICall/movieAPICall.php", true);
    xhttp.setRequestHeader("Content-type", "application/x-www-form-urlencoded");

    xhttp.onreadystatechange = function () {
        if (xhttp.readyState === 4 && xhttp.status == 200) {
            var movieDetails = xhttp.responseText;
            // console.log(JSON.parse(movieDetails));
            return JSON.parse(movieDetails);
            // console.log(movieDetails);
            // showCard(movieDetails);
        }
    };
    
    xhttp.send('movieTitle=' + search);
    // END XMLHttpRequest
}


function searchbutton() {
    var search = document.getElementById('textbar').value;
    if(!specialChar(search)) {
        console.log(search);
        document.getElementById('textbar').value = '';
        // need to make sure that it shows search input was not valid...
        return;
    }

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
    testCard();
}

function deleteCards() {
    for (var i = 0; i < amountofCards; i++) {
        var cardid = "card" + i;
        var card = document.getElementById(cardid);
        card.remove();
    }
    amountofCards = 0;
}

function testCard(){
    const container = document.getElementById('cardcontainerID');

    for(var i = 0; i < 10; i++) {
            const content = `
            <div class="card" id="card${i}">
                <div class="imagebox">
                    <img class="poster" src="https://image.tmdb.org/t/p/original/jRXYjXNq0Cs2TcJjLkki24MLp7u.jpg"/>
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
                <h3>Avatar</h3>
                <div class="hover-content">
                    <a href="" class="cardbutton">A</a>
                    <a href="" class="cardbutton">B</a>
                    <a href="" class="cardbutton">C</a>
                </div>
            </div>
        `;

            // Append newyly created card element to the container
            container.innerHTML += content;
            amountofCards++;
    }

    for (var j = 0; j < 10; j++) {
        var cardid = "card" + j;
        var card = document.getElementById(cardid);
        changeOpacity(card);
    }

}

function changeOpacity(card) {
    setTimeout(() => {
        card.style.opacity = '1';
    }, 500);
}

function showCard(){
    const container = document.getElementById('cardcontainerID');
    var information_available = true;

    const options = {
        method: 'GET',
        headers: {
            'X-RapidAPI-Key': '4a90c0cc84mshe4455be523837acp163521jsnc5e366760b07',
            'X-RapidAPI-Host': 'streaming-availability.p.rapidapi.com'
        }
    };

    const apiRequestURL = buildURL();

    fetch(apiRequestURL, options)
	.then(response => response.json())
	.then(response => {
		response.result.forEach(data => {
            const filmTitle = data.title;
            const filmPoster = data.posterURLs.original;
            var filmService = "Not legally available";
            // MAKE ARRAY INSTEAD OF IF ELSE -> MULTIPLE STREAMING SERVICES
            if(data.streamingInfo.hasOwnProperty("nl")) {
				if(data.streamingInfo.nl.hasOwnProperty("prime")) {
					filmService = "streaming_img/prime.png";
				} else if(data.streamingInfo.nl.hasOwnProperty("netflix")) {
					filmService = "streaming_img/netflix.png";
				} else if(data.streamingInfo.nl.hasOwnProperty("disney")) {
					filmService = "streaming_img/disney.png";
				} else if(data.streamingInfo.nl.hasOwnProperty("hbo")) {
					filmService = "streaming_img/hbo.png";
				} else if(data.streamingInfo.nl.hasOwnProperty("hulu")) {
					filmService = "streaming_img/hulu.png";
				} else if(data.streamingInfo.nl.hasOwnProperty("apple")) {
                    filmService = "streaming_img/apple.png";
                } else {
                    information_available = false;
                }
			} else {
                information_available = false;
            }

            if (information_available == true) {
                const content = `
                <div class="card">
                    <div class="imagebox">
                        <img src="${filmPoster}" class="poster">
                        <div class="streamingservice">
                            <img src="${filmService}">
                        </div>
                    </div>
                    <div class="details">
                        <h3>${filmTitle}</h3>
                    </div>
                </div>
            `;

                // Append newyly created card element to the container
                container.innerHTML += content;
            } else {
                information_available = true;
            }
			// Construct card content
		})
	})
	.catch(err => console.error(err));
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
