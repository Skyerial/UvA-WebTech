async function sendData(id, playlist) {
    const body = JSON.stringify({
        id: id,
        playlist: playlist
    })

    //console.log(JSON.parse(body));
    const request = new Request('add_to_playlist.php', {
        method: 'POST', body: body
    });
    //fetch(request).then(response => response.text()).then(result => console.log(result));
    try {
        const response = await fetch(request);
        if (response.status != 200) {
            console.log("response is not 200");
        }
        const text = await response.text();
        if (text == "not logged in") {
            alert("Log in to make use of this functionality");
        }
        //console.log(text);
    } catch (error) {
        console.log("an error occured");
    }

}

// addToPlaylist("future watching")
function to_watch(id) { sendData(id, "future watching", "add"); }

function cur_watching(id) { sendData(id, "currently watching", "add"); }

function watched(id) { sendData(id, "finished watching", "add"); }
