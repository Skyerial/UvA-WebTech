function searchbutton() {
    var search = document.getElementById('textbar').value;
    document.getElementById('textbar').value = search;

    var x = document.getElementById("title");
    var z = document.getElementById("titletext");
    var e = document.getElementById("contentID");

    if (x.style.display === "none") {

    } else {
        x.style.height = '0px'
        z.style.fontSize = '0px'
        e.style.background = '#6c848c'
        var delayInMilliseconds = 1500;

        setTimeout(function() {
            x.style.display = "none";

        }, delayInMilliseconds);
    }

    var y = document.getElementById("navdiv");
    y.style.paddingBottom = '2vh';
    showCard();
}

function showCard(){
    const container = document.getElementById('cardcontainerID');

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
            var filmService = "Not legally available";
            if(data.streamingInfo.hasOwnProperty("nl")) {
				if(data.streamingInfo.nl.hasOwnProperty("prime")) {
					filmService = "prime";
				} else if(data.streamingInfo.nl.hasOwnProperty("netflix")) {
					filmService = "netflix";
				} else if(data.streamingInfo.nl.hasOwnProperty("disney")) {
					filmService = "disney";
				} else if(data.streamingInfo.nl.hasOwnProperty("hbo")) {
					filmService = "hbo";
				} else if(data.streamingInfo.nl.hasOwnProperty("hulu")) {
					filmService = "hulu";
				} else if(data.streamingInfo.nl.hasOwnProperty("apple")) {
                    filmService = "apple";
                }
			}
			// Construct card content
            const content = `
            <div class="card">
                <h5>${filmTitle}</h5>
                <p>${filmService}</p>
            </div>
        `;

        // Append newyly created card element to the container
        container.innerHTML += content;
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
