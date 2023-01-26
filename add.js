//const filmTitle = "TestTestTEST";
//const filmPoster = "https://upload.wikimedia.org/wikipedia/commons/thumb/3/3f/Placeholder_view_vector.svg/681px-Placeholder_view_vector.svg.png";
//const filmServiceImg = "https://www.tekstmaatje.nl/wp-content/uploads/2020/06/placeholder.png";

function addToPlaylist(id, service_url, service, playlist) {

    const body = JSON.stringify({
        title: data[id].movieTitle,
        picture: data[id].moviePoster,
        service: service,
        service_url: service_url,
        playlist: playlist,
    })

    console.log(JSON.parse(body));
    const request = new Request('add_to_playlist.php', {
        method: 'POST', body: body
    });
    fetch(request).then(response => response.text()).then(result => console.log(result));
}

function sendData(id, playlist) {
    if (data[id].prime) {
        addToPlaylist(id, data[id].prime, "prime", playlist);
    }
    if (data[id].netflix) {
        addToPlaylist(id, data[id].netflix, "netflix", playlist);
    }
    if (data[id].disney) {
        addToPlaylist(id, data[id].disney, "disney", playlist);
    }
    if (data[id].hbo) {
        addToPlaylist(id, data[id].hbo, "hbo", playlist);
    }
    if (data[id].hulu) {
        addToPlaylist(id, data[id].hulu, "hulu", playlist);
    }
    if (data[id].apple) {
        addToPlaylist(id, data[id].apple, "apple", playlist);
    }

}

// addToPlaylist("future watching")
function to_watch(id) { sendData(id, "future watching"); }

function cur_watching(id) { sendData(id, "currently watching"); }

function watched(id) { sendData(id, "finished watching"); }
