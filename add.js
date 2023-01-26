function addToPlaylist(id, service_url, service, playlist) {

    const body = JSON.stringify({
        movieTitle: data[id].movieTitle,
        moviePoster: data[id].moviePoster,
        service: service,
        service_url: service_url,
        playlist: playlist
    })

    //console.log(JSON.parse(body));
    const request = new Request('add_to_playlist.php', {
        method: 'POST', body: body
    });
    fetch(request).then(response => response.text())
    //.then(result => console.log(result));
}

function sendData(id, playlist) {
    if (data[id].prime) {
        addToPlaylist(id, data[id].prime, "prime", playlist);
    }
    setTimeout(function(){
        if (data[id].netflix) {
            addToPlaylist(id, data[id].netflix, "netflix", playlist);
        }
    }, 500);
    setTimeout(function(){
        if (data[id].disney) {
            addToPlaylist(id, data[id].disney, "disney", playlist);
        }
    }, 1000);
    setTimeout(function(){
        if (data[id].hbo) {
            addToPlaylist(id, data[id].hbo, "hbo", playlist);
        }
    }, 1500);
    setTimeout(function(){
        if (data[id].hulu) {
            addToPlaylist(id, data[id].hulu, "hulu", playlist);
        }
    }, 2000);
    setTimeout(function(){
        if (data[id].apple) {
            addToPlaylist(id, data[id].apple, "apple", playlist);
        }
    }, 2500);

}

// addToPlaylist("future watching")
function to_watch(id) { sendData(id, "future watching"); }

function cur_watching(id) { sendData(id, "currently watching"); }

function watched(id) { sendData(id, "finished watching"); }
